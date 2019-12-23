<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DIARIO DE PRODUCTOR</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/w3.css') }}" rel="stylesheet">
</head>
<body>
<div class="logo">
    <div>
        <img src="logo_pdf.png">
        <br>
    </div>
</div>
<hr>

<br>
<br>
<h1> REPORTE DIARIO DE PRODUCTOR </h1>
<h2> {{$productor->productor_nombre}} </h2>
<h3> {{$fecha}} </h3>

<hr>

<div class="logo">
    <img src="grafico.png">
    <br>
</div>

</body>
</html>
