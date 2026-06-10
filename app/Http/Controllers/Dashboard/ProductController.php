<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\Product;
use App\Models\Category;
use App\Models\Historiale;
use App\Models\Marca;
use App\Models\Equivalencia;
use App\Models\Inventario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Redirect;

use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Picqer\Barcode\BarcodeGeneratorHTML;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $row = (int) request('row', 30);

        if ($row < 1 || $row > 100) {
            abort(400, 'El parámetro por página debe estar entre 1 y 100.');
        }

       $query = Product::select('products.*')
        ->Join('categories as c', 'products.category_id', '=', 'c.id')
        ->Join('marcas as m', 'products.marca_id', '=', 'm.id')
        ->addSelect([
            'c.name as category_name',
            'm.nombre as marca_nombre',
        ]);

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                ->orWhere('codigo_barras', 'like', "%{$search}%")
                ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($marcaId = request('marca_id')) {
            $query->where('marca_id', $marcaId);
        }

        $query = $query->sortable([
            'product_name',
            'codigo_barras',
            'category_name',
            'marca_nombre',
            'supplier_name',
            'selling_price'
        ]);

        $products = $query->paginate($row)->appends(request()->query());

        $categories = Category::all();
        $marcas = Marca::all();


        return view('products.index', compact('products', 'categories', 'marcas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create', [
            'categories' => Category::all(),
            'marcas' => Marca::all(),
            'equivanecias' => Equivalencia::where('activo',1)->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'product_image' => 'image|file|max:1024',
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'marca_id' => 'required|integer',
            'equivalencia_id'=> 'required|integer',
            'product_garage' => 'string|nullable',
            'product_code' => ['nullable','string','unique:products,product_code','regex:/^\d{4,7}$/'],
            'codigo_barras' => ['nullable','string','unique:products,codigo_barras','regex:/^\d{13}$/'],
            'buying_date' => 'date_format:Y-m-d|max:10|nullable',
            'expire_date' => 'date_format:Y-m-d|max:10|nullable',
            'buying_price' => ['required', 'regex:/^(?!0\d)\d{1,7}(\.\d{1,2})?$/'],
            'selling_price' => ['required', 'regex:/^(?!0\d)\d{1,7}(\.\d{1,2})?$/'],
            'dealer_price' => ['required', 'regex:/^(?!0\d)\d{1,7}(\.\d{1,2})?$/'],
        ];

        // Validar primero
        $validatedData = $request->validate($rules);

        $buying = floatval($request->buying_price);
        $selling = floatval($request->selling_price);
        $dealer = floatval($request->dealer_price);

        // Validación personalizada
        if ($selling <= $buying || $dealer <= $buying) {
            return redirect()->back()->withInput()
                    ->with('error', '¡Ups! Hay algunos errores: El precio de venta y distribuidor deben ser mayores al precio de compra.');

        }

        // Si no enviaron código, generar uno automático
        if (empty($validatedData['product_code'])) {
            // Cortar los primeros 3 caracteres del nombre del producto (en slug para evitar espacios y símbolos)
            $productName = Str::slug($validatedData['product_name'], '-');
            $prefix = strtoupper(substr($productName, 0, 3)) . '-';

            $validatedData['product_code'] = IdGenerator::generate([
                'table' => 'products',
                'field' => 'product_code',
                'length' => strlen($prefix) + 4, // Ej. 7 si el prefijo es "ABC-"
                'prefix' => $prefix,
                'reset_on_prefix_change' => true // Opcional si deseas que cada prefijo tenga su propia secuencia
            ]);
        }

        if (empty($validatedData['codigo_barras'])) {
            do {
                // Genera un número aleatorio de 13 dígitos
                $generatedBarcode = str_pad(strval(mt_rand(1, 9999999999999)), 13, '0', STR_PAD_LEFT);
            } while (\App\Models\Product::where('codigo_barras', $generatedBarcode)->exists());

            $validatedData['codigo_barras'] = $generatedBarcode;
        }

        // Estatus fijo
        $validatedData['status_product'] = 1;
            /**
             * Handle upload image with Storage.
             */
        if ($file = $request->file('product_image')) {
                $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
                $path = 'public/products/';

                $file->storeAs($path, $fileName);
                $validatedData['product_image'] = $fileName;
        }

            $producto = Product::create($validatedData);

            // codificacion de historial de actualizacion de precio producto
            $historial = new Historiale();
            $historial->fecha = now()->timezone('America/Mexico_City')->toDateTimeString();
            $historial->accion = 'Creación de producto';
            $historial->descripcion = 'Se ha creado un nuevo producto: con precio de compra: $' . number_format($buying, 2) . ', precio de venta: $' . number_format($selling, 2) . ', precio de distribuidor: $' . number_format($dealer, 2);
            $historial->user_id = auth()->id();
            $historial->product_id = $producto->id;
            $historial->save();


        return Redirect::route('products.index')->with('success', 'Producto creado exitosamente!');
    }

    public function show(Product $product)
    {
        // Barcode Generator
        $generator = new BarcodeGeneratorHTML();

        $barcode = $generator->getBarcode($product->product_code, $generator::TYPE_CODE_128);

        return view('products.show', [
            'product' => $product,
            'barcode' => $barcode,
        ]);
    }


    public function edit(Product $product)
    {
        return view('products.edit', [
            'categories' => Category::all(),
            'marcas' => Marca::all(),
            'equivanecias' => Equivalencia::where('activo',1)->get(),
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $rules = [
            'product_image' => 'image|file|max:1024',
            'product_name' => 'required|string',
            'category_id' => 'required|integer',
            'product_garage' => 'string|nullable',
            'equivalencia_id'=> 'required|integer',
            'buying_date' => 'date_format:Y-m-d|max:10|nullable',
            'product_code' => ['required','string', Rule::unique('products', 'product_code')->ignore($product->id),'regex:/^[A-Za-z0-9\-]{4,7}$/',],
           'codigo_barras' => ['required','string', Rule::unique('products', 'codigo_barras')->ignore($product->id),'regex:/^\d{13}$/',],
            'expire_date' => 'date_format:Y-m-d|max:10|nullable',
            'buying_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'selling_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'dealer_price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
        ];

        $validatedData = $request->validate($rules);

        $buying = floatval($request->buying_price);
        $selling = floatval($request->selling_price);
        $dealer = floatval($request->dealer_price);


        // Validación personalizada
        if ($selling <= $buying || $dealer <= $buying) {
            return redirect()->back()->withInput()
                    ->with('error', '¡Ups! Hay algunos errores: El precio de venta y distribuidor deben ser mayores al precio de compra.');
        }

        // codificacion de historial de actualizacion de precio producto
        $producto = Product::find($product->id);
        if($producto->buying_price != $buying || $producto->selling_price != $selling || $producto->dealer_price != $dealer) {
            $descripcion = [];

            if ($producto->buying_price != $buying) {
                $descripcion[] = "Compra: $" . number_format($producto->buying_price, 2) . " → $" . number_format($buying, 2);
            }
            if ($producto->selling_price != $selling) {
                $descripcion[] = "Venta: $" . number_format($producto->selling_price, 2) . " → $" . number_format($selling, 2);
            }
            if ($producto->dealer_price != $dealer) {
                $descripcion[] = "Distribuidor: $" . number_format($producto->dealer_price, 2) . " → $" . number_format($dealer, 2);
            }
                $historial = new Historiale();
                $historial->fecha = now()->timezone('America/Mexico_City')->toDateTimeString();
                $historial->accion = 'Actualización de precios';
                $historial->descripcion = implode(", ", $descripcion);
                $historial->user_id = auth()->id();
                $historial->product_id = $product->id;
            $historial->save();
        }

        if ($file = $request->file('product_image')) {
            $fileName = hexdec(uniqid()).'.'.$file->getClientOriginalExtension();
            $path = 'public/products/';

            /**
             * Delete photo if exists.
             */
            if($product->product_image){
                Storage::delete($path . $product->product_image);
            }

            $file->storeAs($path, $fileName);
            $validatedData['product_image'] = $fileName;
        }

        Product::where('id', $product->id)->update($validatedData);

        return Redirect::route('products.index')->with('success', '¡El producto ha sido actualizado!');
    }


    public function destroy(Product $product)
    {
        // Verificar si el producto está presente en algún inventario
        $existeEnInventario = Inventario::where('product_id', $product->id)->exists();

        if ($existeEnInventario) {
            // No se puede eliminar porque hay inventario relacionado
            return Redirect::route('products.index')
                ->with('error', '¡No se puede eliminar el producto porque tiene inventario registrado!');
        }

        // Eliminar la imagen si existe
        if ($product->product_image) {
            Storage::delete('public/products/' . $product->product_image);
        }

        // Eliminar el producto
        $product->delete();

        return Redirect::route('products.index')
            ->with('success', '¡El producto ha sido eliminado exitosamente!');
    }


    /**
     * Show the form for importing a new resource.
     */
    public function importView()
    {
        return view('products.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'upload_file' => 'required|file|mimes:xls,xlsx',
        ]);

        $the_file = $request->file('upload_file');

        try{
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range( 2, $row_limit );
            $column_range = range( 'J', $column_limit );
            $startcount = 2;
            $data = array();
            foreach ( $row_range as $row ) {
                $data[] = [
                    'product_name' => $sheet->getCell( 'A' . $row )->getValue(),
                    'category_id' => $sheet->getCell( 'B' . $row )->getValue(),
                    'supplier_id' => $sheet->getCell( 'C' . $row )->getValue(),
                    'product_code' => $sheet->getCell( 'D' . $row )->getValue(),
                    'product_image' => $sheet->getCell( 'F' . $row )->getValue(),
                    'buying_date' =>$sheet->getCell( 'H' . $row )->getValue(),
                    'expire_date' =>$sheet->getCell( 'I' . $row )->getValue(),
                    'buying_price' =>$sheet->getCell( 'J' . $row )->getValue(),
                    'selling_price' =>$sheet->getCell( 'K' . $row )->getValue(),
                ];
                $startcount++;
            }

            Product::insert($data);

        } catch (Exception $e) {
            // $error_code = $e->errorInfo[1];
            return Redirect::route('products.index')->with('error', '¡Hubo un problema al cargar los datos!');
        }
        return Redirect::route('products.index')->with('success', '¡Los datos se han importado correctamente!');
    }

    public function exportExcel($products){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($products);
            $Excel_writer = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Products_ExportedData.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $Excel_writer->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    function exportData(){
        $products = Product::all()->sortByDesc('product_id');

        $product_array [] = array(
            'Product Name',
            'Category Id',
            'Supplier Id',
            'Product Code',
            'Product Garage',
            'Product Image',
            'Product Store',
            'Buying Date',
            'Expire Date',
            'Buying Price',
            'Selling Price',
        );

        foreach($products as $product)
        {
            $product_array[] = array(
                'Product Name' => $product->product_name,
                'Category Id' => $product->category_id,
                'Supplier Id' => $product->supplier_id,
                'Product Code' => $product->product_code,
                'Product Garage' => $product->product_garage,
                'Product Image' => $product->product_image,
                'Product Store' =>$product->product_store,
                'Buying Date' =>$product->buying_date,
                'Expire Date' =>$product->expire_date,
                'Buying Price' =>$product->buying_price,
                'Selling Price' =>$product->selling_price,
            );
        }

        $this->ExportExcel($product_array);
    }

}
