@if ($products->count())
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>@sortablelink('name', 'Nombre')</th>
                    <th>Precio Venta</th>
                    <th>Stock</th>
                    <th>Expira</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>${{ number_format($product->selling_price, 2) }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>{{ \Carbon\Carbon::parse($product->expire_date)->format('d/m/Y') }}</td>
                    <td>
                        <form action="{{ route('pos.addCart') }}" method="POST"  style="margin-bottom: 5px">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $product->id }}">
                                            <input type="hidden" name="name" value="{{ $product->product_name }}">
                                            <input type="hidden" name="price" value="{{ $product->selling_price }}">

                                            <button type="submit" class="btn btn-primary border-none" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add"><i class="far fa-plus mr-0"></i></button>
                                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $products->links() }}
    </div>
@else
    <div class="alert alert-warning text-center">
        No se encontraron productos.
    </div>
@endif
