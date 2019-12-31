<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DIARIO DE PRODUCTOR</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/w3.css') }}" rel="stylesheet">

</head>
<body>
<div class="row">
    <div class="logo" align="left">
        <div>
            <img src="logo_pdf.png">

        </div>
    </div>
</div>
<br>
<br>


<hr>
<div class="row">
    <h3 align="center"> REPORTE DIARIO DE PRODUCTOR </h3>
    <h3 align="center"> {{$productor->productor_nombre ?? "caca"}}  </h3>
    <h3 align="center"> {{$fecha ?? "picji"}} </h3>
</div>
<hr>


<div>
    <img src="grafico.png">
    <br>
</div>

</body>
</html>
