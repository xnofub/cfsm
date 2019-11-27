@extends('layouts.web')
@section('title', 'Dashboard')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">Home</a>
        </li>
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                        Resumen Calidad
                    </button>
                </h5>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body">
                    <div class="row">
                        @foreach($result as $res)
                            <div class="col-xl-2 col-md-4 mb-3">
                                <div class="card border-left-{{$res['tag']}} shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div
                                                    class="text-xs font-weight-bold text-{{$res['tag']}} text-uppercase mb-1">
                                                    Calificación-{{$res['nombre']}}
                                                </div>
                                                <div
                                                    class="h5 mb-0 font-weight-bold text-{{$res['tag']}} text-gray-800">
                                                    Lotes: {{$res['cantidad']}}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-plus-circle fa-2x "></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>


    </div>

    <div>
        <div class="row">


            <div class="col-xl-12 col-md-12 mb-12">
                    {!! Form::open(['route' => 'dashboard', 'method' => 'POST', 'class' => '','role'=>'form']) !!}

                <div class="card-body col-xl-2 col-md-2 mb-2">
                    <div class="text-xs font-weight-bold  text-uppercase mb-1">Filtros</div>
                </div>
                <div class="row col-xl-12 col-md-12 mb-12">

                        <div class="form-group col-xl-6 col-md-6 mb-6">
                                {!! Form::label('region_id', 'Region', array('class' => '')) !!}
                                <select class='form-control' id='region_id' name='region_id'>
                                    <option value=""> Seleccione una región </option>
                                    @foreach ($regiones as $r)
                                        <option value="{{$r->region_id}}" @isset($muestra->region_id) {{ $muestra->region_id == $r->region_id ? 'selected' : '' }} @endisset > {{$r->region_nombre}}</option>
                                    @endforeach
                                </select>
                            </div>



                            <div class="form-group col-xl-6 col-md-6 mb-6">
                                    {!! Form::label('productor_id', 'Productor', array('class' => '')) !!}
                                    <select class='form-control' id='productor_id' name='productor_id'>
                                            @foreach ($productores as $p)
                                                <option value="{{$p->productor_id}}" @isset($muestra->region_id)  {{ $muestra->productor_id == $p->productor_id ? 'selected' : '' }} @endisset > {{$p->productor_nombre}}  </option>
                                            @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-xl-4 col-md-4 mb-4">

                                        <!--<button type="submit" class="btn btn-primary btn_ok btn-block">Actualizar <i class="far fa-caret-square-right"></i> </button>-->
                                    </div>



                </div>



            </div>



            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card-body">
                    <div class="text-xs font-weight-bold  text-uppercase mb-1">Acumulado</div>
                    <canvas id="chartPorcentajes" width="100%" height="50">dfg</canvas>
                </div>
            </div>

            <div class="col-xl-12 col-md-12 mb-12">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold  text-uppercase mb-1">Promedio de Defectos Generales</div>
                        <canvas id="defectosPorcentaje" width="100%" height="50"></canvas>
                    </div>
                </div>
        </div>
    </div>


@endsection
@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.js"></script>

<script type="text/JavaScript">


        var ctxP = document.getElementById("chartPorcentajes").getContext('2d');
        var chartPorcentajes = new Chart(ctxP, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($result  as $res)
                        "{{$res['nombre']}} {{$res['porcentaje']}}%",
                    @endforeach
                ],
                datasets: [{
                    data: [
                        @foreach($result  as $res)
                        {{$res['porcentaje']}},
                        @endforeach
                    ],
                    backgroundColor: [
                        @foreach($result  as $res)
                            "{{$res['color']}}",
                        @endforeach
                    ],
                    hoverBackgroundColor: [
                            @foreach($result  as $res)
                                "{{$res['color_bg']}}",
                            @endforeach
                        ]
                    }],
                    options: {
                        responsive: true
                    }
                },
            });



            var ctxP = document.getElementById("defectosPorcentaje").getContext('2d');
            var defectosPorcentaje = new Chart(ctxP, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($data  as $res)
                            "{{$res['nombre']}}",
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Promedio de Defectos',
                        data: [
                            @foreach($data  as $res)
                            {{$res['promedioPorcentaje']}},
                            @endforeach
                        ],
                        backgroundColor: [
                            @foreach($data  as $res)
                                "#{{$res['color']}}",
                            @endforeach
                        ]
                    }],
                    options: {
                        responsive: true
                    }
                },
            });

        function actualizaGraficosGastos(datosGraficos) {

            var dataIngresoReal = [];
            var dataIngresoProyectado = [];
            var gastoReal = [];
            var gastoProyectado = [];


            var defectosPorcentajeArray = [];
            var chartPorcentajes = [];

            $.each(datosGraficos, function(i, item) {
                dataIngresoReal.push(datosGraficos[i].value_ingreso);
                dataIngresoProyectado.push(datosGraficos[i].value_pry_ingreso);

                gastoReal.push(datosGraficos[i].value_gasto);
                gastoProyectado.push(datosGraficos[i].value_pry_gasto);
            });

            console.log("gastoReal"+gastoReal);
            console.log(gastoProyectado);
            ingresoProyectadoChart.data.datasets[0].data = dataIngresoReal;
            ingresoProyectadoChart.data.datasets[1].data = dataIngresoProyectado;
            ingresoProyectadoChart.update();

            console.log(dataIngresoReal);
            console.log(dataIngresoProyectado);
            gastoProyectadoChart.data.datasets[0].data = gastoReal;
            gastoProyectadoChart.data.datasets[1].data = gastoProyectado;
            gastoProyectadoChart.update();
        }

        function actualizaDefectosPorcentaje(data) {

            var defectosPorcentajeArray = [];

            $.each(data, function(i, item) {
                defectosPorcentajeArray.push(data[i].promedioPorcentaje);
            });
            console.log(defectosPorcentajeArray);

            defectosPorcentaje.data.datasets[0].data = defectosPorcentajeArray;
            defectosPorcentaje.update();

        }

        function actualizachartPorcentajes(data) {

            var chartPorcentajesArray = [];

            $.each(data, function(i, item) {
                chartPorcentajesArray.push(data[i].porcentaje);
            });
            chartPorcentajes.data.datasets[0].data = chartPorcentajesArray;
            chartPorcentajes.update();

        }



        $( "#region_id" ).change(function() {
            var route = "{!!URL::to('/getProductoresByRegionId')!!}";
            //alert(route);
            var region_id = $("#region_id" ).val();
            var select = $("#productor_id");
            var token = $("input[name=_token]").val();
            $("#productor_id option").remove();
             $.post( route, { region_id: region_id , _token : token })
             .done(function( data ) {
                 $(data).each(function( index, value ) {
                        select.append("<option value='"+value.id+"'> "+value.nombre+"</option>");
                        console.log( value.id + value.nombre );
                 });
             });
         });

         $( "#productor_id" ).change(function() {
            var route = "{!!URL::to('/getDataByProductoresId')!!}";
            var region_id = $("#region_id" ).val();
            var productor_id = $("#productor_id" ).val();
            var token = $("input[name=_token]").val();
             $.post( route, {productor_id: productor_id, region_id: region_id , _token : token })
             .done(function( data ) {
                 var contador = 0;
                 $.each(data, function(i, item) {

                    //console.log(item);
                    //console.log(i);
                    //console.log(contador);

                    if(contador == 0) {
                        actualizaDefectosPorcentaje(item);
                    }
                    if(contador == 1) {
                        actualizachartPorcentajes(item);
                    }
                    contador++;
                });
             });
         });
    </script>

@endsection
