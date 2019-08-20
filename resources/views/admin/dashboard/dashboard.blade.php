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
            <div class="col-xl-6 col-md-6 mb-6">
                <div class="card-body">
                    <div class="text-xs font-weight-bold  text-uppercase mb-1">Acumulado</div>
                    <canvas id="chartPorcentajes" width="100%" height="50">dfg</canvas>
                </div>
            </div>
            <div class="col-xl-6 col-md-6 mb-6">
                <div class="card-body">
                    <div class="text-xs font-weight-bold  text-uppercase mb-1">Principales Productores
                    </div>
                    <canvas id="chartMuestras" width="100%" height="50">dfg</canvas>
                </div>
            </div>

            <div class="col-xl-12 col-md-12 mb-12">
                <div class="card-body">
                    <div class="text-xs font-weight-bold  text-uppercase mb-1">Promedio de Defectos</div>
                    <canvas id="defectosPorcentaje" width="150%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('js')
    <script type="text/JavaScript">
        $(function () {

            var ctxP = document.getElementById("chartPorcentajes").getContext('2d');
            var myPieChart = new Chart(ctxP, {
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
        });


        $(function () {
            var ctxP = document.getElementById("defectosPorcentaje").getContext('2d');

            var myPieChart = new Chart(ctxP, {
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
        });


        $(function () {
            var ctxP = document.getElementById("chartMuestras").getContext('2d');

            var myPieChart = new Chart(ctxP, {
                type: 'horizontalBar',
                data: {
                    labels: [
                        @foreach($cantRes  as $res)
                            "{{$res['nombre']}}",
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Promedio de Defectos',

                        data: [
                            @foreach($cantRes  as $res)
                            {{$res['cantidad']}},
                            @endforeach
                        ],
                        backgroundColor: [
                            @foreach($result  as $res)
                                "{{$res['color']}}",
                            @endforeach
                        ]
                    }],
                    options: {
                        responsive: true
                    }
                },
            });
        });
    </script>

@endsection
