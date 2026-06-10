<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\Caja;
use App\Models\Branche;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{


    public function index()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe ser un número entre 1 y 100.');
        }

        $users = User::query()
            ->join('branches', 'branches.id', '=', 'users.branche_id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.photo',
                'users.username',
                'users.apellido_p',
                'users.apellido_m',
                'branches.nombre as branch_name'
            )
            ->when(request('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'like', '%' . $search . '%')
                        ->orWhere('users.email', 'like', '%' . $search . '%');
                });
            })
            ->when(request('branch'), function ($query, $branchId) {
                $query->where('users.branche_id', $branchId);
            })
            ->sortable()
            ->paginate($row)
            ->appends(request()->query());

        $branches = Branche::all(); // obtener lista de sucursales

        return view('users.index', compact('users', 'branches'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('users.create', [
        //     'roles' => Role::all(),
        // ]);
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            // dd($request->all());

            $rules = [
            'name' => 'required|String|max:50',
            'apellido_p' => 'required|String|max:50',
            'apellido_m' => 'nullable|String|max:50',
            'photo' => 'nullable|image|file|max:1024',
            'email' => ['required', 'string','max:50','unique:users,email','regex:/^[^@\s]+@[^@\s]+\.com$/i'],
            'username' => 'required|min:4|max:25|alpha_dash:ascii|unique:users,username',
            'branche_id' => 'required|exists:branches,id',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|exists:roles,id',
            'cellphone' => ['required','string','regex:/^[0-9]{7,15}$/',],
            'country_code' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        // Limpieza y armado del número telefónico completo
        $rawCellphone = preg_replace('/[^0-9]/', '', $request->cellphone);
        if ($request->country_code === '+52') {
            $fullCellphone = $request->country_code . '1' . $rawCellphone;
        } else {
            $fullCellphone = $request->country_code . $rawCellphone;
        }

        // Validar que el número completo sea único
        if (\App\Models\User::where('cellphone', $fullCellphone)->exists()) {
            return back()->withErrors(['cellphone' => 'Este número ya está registrado.'])->withInput();
        }

        $validatedData['cellphone'] = $fullCellphone;
        $validatedData['estado'] = 1; // Estado activo por defecto
        $validatedData['password'] = Hash::make($request->password);

        // Validar rol por nombre usando el ID
        $role = Role::find($request->role);

        if ($role) {
            if ($role->name === 'SuperAdmin') {
                if (User::role('SuperAdmin')->exists()) {
                    return back()->withErrors(['role' => 'Ya existe un usuario con el rol de Super Admin.'])->withInput();
                }
            }

            if ($role->name === 'Propietario') {
                if (User::role('Propietario')->exists()) {
                    return back()->withErrors(['role' => 'Ya existe un usuario con el rol de Propietario.'])->withInput();
                }
            }

            if ($role->name === 'Gerente') {
                $existeGerente = User::role('Gerente')
                    ->where('branche_id', $request->branche_id)
                    ->exists();

                if ($existeGerente) {
                    return back()->withErrors(['role' => 'Ya existe un Gerente asignado en esta sucursal.'])->withInput();
                }
            }
        }

        // Guardar imagen si se envió
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/profile/';
            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        $user = User::create($validatedData);
        $user->assignRole($role);

        return Redirect::route('users.index')->with('success', 'Usuario creado exitosamente!');
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(User $user)
    {
        $selectedCode = null;
        if (!empty($user->cellphone) && strlen($user->cellphone) >= 3) {
            $selectedCode = substr($user->cellphone, 0, 3);
        }

        return view('users.edit', [
            'userData' => $user,
            'roles' => Role::all(),
            'selectedCode' => $selectedCode,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $rules = [
            'name' => 'required|max:50',
            'photo' => 'image|file|max:1024',
            'apellido_p' => 'required|max:50',
            'apellido_m' => 'max:50',
            'estado' => 'required|boolean',
            'branche_id' => 'required|exists:branches,id',
            'email' => 'required|email|max:50|unique:users,email,' . $user->id,
            'username' => 'required|min:4|max:25|alpha_dash:ascii|unique:users,username,' . $user->id,
            'role' => 'required|exists:roles,id',
            'cellphone' => ['required','string','regex:/^[0-9]{7,15}$/',],
            'country_code' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        $rawCellphone = preg_replace('/[^0-9]/', '', $request->cellphone);
        if ($request->country_code === '+52') {
            $fullCellphone = $request->country_code . '1' . $rawCellphone;
        } else {
            $fullCellphone = $request->country_code . $rawCellphone;
        }

        $exists = \App\Models\User::where('cellphone', $fullCellphone)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['cellphone' => 'Este número ya está registrado.'])->withInput();
        }

        $validatedData['cellphone'] = $fullCellphone;

        if ($request->filled('password')) {
            $rules['password'] = 'min:6|required_with:password_confirmation';
            $rules['password_confirmation'] = 'min:6|same:password';
        }


        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($request->password);
        } else {
            unset($validatedData['password']);
        }

        // Verificar rol actual solicitado
        $role = Role::find($request->role);
        if ($role) {
            if ($role->name === 'SuperAdmin') {
                $existeSuperAdmin = User::role('SuperAdmin')
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($existeSuperAdmin) {
                    return back()->withErrors(['role' => 'Ya existe otro usuario con el rol de Super Admin.'])->withInput();
                }
            }

            if ($role->name === 'Propietario') {
                $existePropietario = User::role('Propietario')
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($existePropietario) {
                    return back()->withErrors(['role' => 'Ya existe otro usuario con el rol de Propietario.'])->withInput();
                }
            }

            if ($role->name === 'Gerente') {
                $existeGerente = User::role('Gerente')
                    ->where('branche_id', $request->branche_id)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($existeGerente) {
                    return back()->withErrors(['role' => 'Ya existe otro Gerente asignado en esta sucursal.'])->withInput();
                }
            }
        }

        // Guardar nueva imagen si se cargó
        if ($file = $request->file('photo')) {
            $fileName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $path = 'public/profile/';

            if ($user->photo) {
                Storage::delete($path . $user->photo);
            }

            $file->storeAs($path, $fileName);
            $validatedData['photo'] = $fileName;
        }

        $user->update($validatedData);

        if ($request->role) {
            $user->syncRoles($request->role);
        }

        return Redirect::route('users.index')->with('success', 'Usuario actualizado exitosamente!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $count = Caja::where('user_id', $user->id)->count();

        if($count > 0) {
            return Redirect::route('users.index')->with(['error' => 'No se puede eliminar el usuario porque tiene cajas asociadas.']);
        }

        if($user->photo){
            Storage::delete('public/profile/' . $user->photo);
        }

        User::destroy($user->id);

        return Redirect::route('users.index')->with('success', 'Usuario eliminado!');

    }

    // metodo para obtener roles disponibles en una sucursal excluyendo el superAdmin y el gerente si ya existe uno en la sucursal
    public function obtenerRolesDisponibles($sucursalId)
    {
        $roles = Role::where('name', '!=', 'SuperAdmin')->get(); // Siempre excluir SuperAdmin

        // Excluir Propietario si ya existe uno asignado
        $hayPropietario = User::role('Propietario')->exists();
        if ($hayPropietario) {
            $roles = $roles->filter(function ($role) {
                return $role->name !== 'Propietario';
            });
        }

        // Verificar si ya hay un Gerente en la sucursal
        $hayGerente = User::where('branche_id', $sucursalId)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Gerente');
            })
            ->exists();

        if ($hayGerente) {
            $roles = $roles->filter(function ($role) {
                return $role->name !== 'Gerente';
            });
        }

        return response()->json($roles->values());
    }
}
