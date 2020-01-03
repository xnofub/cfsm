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
    <div>
        <div>
            <img src="logo_png.png"">

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
</div>

@if($response)
    <hr>
    <div>
        <table border="1">
            <thead>
            <tr>
                <td>Calificacion</td>
                <td>Numero de Pallet</td>
                <td>Defecto</td>
                <td>Porcentaje</td>
                <td>Numero de Muestras</td>
            </tr>
            </thead>
            <tbody>
            @foreach($response as $item)
                <tr>
                    <td>{{$item['calificacion']}}</td>
                    <td>{{$item['pallet']}}</td>
                    <td>{{$item['defecto']}}</td>
                    <td>{{$item['porcentaje']}}</td>
                    <td>{{$item['num_muestras']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endif


</body>
</html>
