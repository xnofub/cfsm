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
            <img src="logonuevo.png">
        </div>
    </div>


</div>

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

@if($cantidadShow)
    <hr>
    <div>
        <table border="1">
            <thead>
            <tr>
                <td>Nota</td>
                <td>Cantidad de Pallet</td>
            </tr>
            </thead>
            <tbody>
            @foreach($cantidad as $item => $value)
                <tr>
                    <td>{{$item}}</td>
                    <td>{{$value['pallets']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($response)
    <br>
    <hr>
    <div>
        <table border="1">
            <thead>
            <tr>

                <td>Num. de Pallet</td>
                <td>Codigo Muestra(QR)</td>
                <td>Nota Muestra</td>
                <td>Variedad</td>
                <td>Defecto</td>
                <td>Porcentaje</td>
                <!--<td>Num. de Muestras</td>-->
            </tr>
            </thead>
            <tbody>
            @foreach($response as $item)
                @if($item['calificacion'] == 'C' || $item['calificacion'] == 'O')
                    <tr>

                        <td>{{$item['pallet']}}</td>
                        <td>{{$item['qr']}}</td>
                        <td>{{$item['calificacion']}}</td>
                        <td>{{$item['variedad']}}</td>
                        <td>{{$item['defecto']}}</td>
                        <td>{{$item['porcentaje']}}</td>
                       <!-- <td>{{$item['num_muestras']}}</td>-->
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if($imagesShow)
    @foreach($images as $image)
        <br>
        <br>
        <br>
        <hr>
        <div>
            <label>{{$image['description']}}</label>
            <img src="{{$image['path']}}" alt="{{$image['description']}}">
        </div>
    @endforeach
@endif


</body>
</html>
