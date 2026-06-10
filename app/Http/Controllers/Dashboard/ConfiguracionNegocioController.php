<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Models\ConfiguracionNegocio;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ConfiguracionNegocioController extends Controller
{
    public function index()
    {
        $config = ConfiguracionNegocio::firstOrCreate(
            [],
            ['nombre_negocio' => 'Mi Negocio']
        );
        return view('configuracion.negocio', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre_negocio' => 'required|string|max:100',
            'telefono' => 'nullable|digits:10',
            'logo'           => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'favicon'        => 'nullable|image|mimes:png,ico,jpg|max:512',
        ]);

        $config = ConfiguracionNegocio::firstOrCreate(
            [],
            ['nombre_negocio' => 'Mi Negocio']
        );

        $data = $request->only(['nombre_negocio', 'telefono']);

        // Subir logo
        if ($request->hasFile('logo')) {
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo); // ✅ sin prefijo duplicado
            }
            $data['logo'] = $request->file('logo')->store('negocio', 'public');
        }

        // Subir favicon
        if ($request->hasFile('favicon')) {
            if ($config->favicon) {
                Storage::disk('public')->delete($config->favicon); // ✅
            }
            $data['favicon'] = $request->file('favicon')->store('negocio', 'public');
        }

        $config->update($data);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function deleteLogo()
    {
        $config = ConfiguracionNegocio::first();
        if ($config && $config->logo) {
            Storage::disk('public')->delete($config->logo); // ✅
            $config->update(['logo' => null]);
        }
        return back()->with('success', 'Logo eliminado.');
    }

    public function deleteFavicon()
    {
        $config = ConfiguracionNegocio::first();
        if ($config && $config->favicon) {
            Storage::disk('public')->delete($config->favicon); // ✅
            $config->update(['favicon' => null]);
        }
        return back()->with('success', 'Favicon eliminado.');
    }

}

