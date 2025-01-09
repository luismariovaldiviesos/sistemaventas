<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
</head>
<body>
    <p>Estimado(a) {{ $factura->cliente->businame }},</p>
    <p>Adjunto encontrará la factura N° {{ $factura->secuencial }} en formato PDF y XML.</p>
    <p>Gracias por su preferencia.</p>
</body>
</html>
