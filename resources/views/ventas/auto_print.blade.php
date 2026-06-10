<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión Automática</title>
    <script src="{{ asset('assets/js/rsvp-3.1.0.min.js') }}"></script>
<script src="{{ asset('assets/js/sha-256.min.js') }}"></script>
<script src="{{ asset('assets/js/qz-tray.js') }}"></script>


</head>
<body>
    <h4>Imprimiendo ticket... Espere</h4>

    <script>
       document.addEventListener("DOMContentLoaded", async function () {
    try {
        await qz.websocket.connect();

        const printer = await qz.printers.find("GTP801 Printer"); // Ajusta el nombre de tu impresora
        // const config = qz.configs.create(printer, { encoding: 'utf-8' });
        const config = qz.configs.create(printer, { encoding: 'cp437' });


        const ticket = await fetch("{{ route('ticket.raw', ['id' => $venta->id]) }}")
            .then(response => response.text());

        // Envía ticket y luego el comando de corte
        await qz.print(config, [
            { type: 'raw', format: 'plain', data: ticket },
            { type: 'raw', format: 'hex',  data: '1d5600' }
        ]);

        qz.websocket.disconnect();
        window.location.href = "{{ route('ventas.index') }}";
    } catch (e) {
        alert("Error al imprimir: " + e);
        console.error(e);
        window.location.href = "{{ route('ventas.index') }}";
    }
});

    </script>
</body>
</html>
