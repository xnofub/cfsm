<!DOCTYPE html>
<html>
<head>
    <title>REPORTE DIARIO DE PRODUCTOR</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/w3.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<div class="row">
    <div class="logo" align="left">
        <div>
            <img src="logo_pdf.png">
            <br>
        </div>
    </div>

</div>


<br>
<br>

<div class="row">

    <div class="form-control">
        <label align="center"> REPORTE DIARIO DE PRODUCTOR </label>
    </div>
    <br>
    <div class="form-control">
        <label align="center"> {{$productor->productor_nombre ?? "caca"}} </label>
    </div>
    <br>
    <div class="form-control">
        <label align="center"> {{$fecha ?? "picji"}} </label>
    </div>
</div>


<hr>

<div>
    <img src="grafico.png">
    <br>
</div>

</body>
</html>
