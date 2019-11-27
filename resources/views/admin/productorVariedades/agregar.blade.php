@extends('layouts.web')
@section('title', 'Productor/Variedad')
@section('content')


    @include('layouts.error')

    <div class="modal-header">
        <h4 class="modal-title titulo_formulario" id="">Agregar Productor/Variedad</h4>
    </div>
    <div class="modal-body">
        @include('admin.productorVariedades.form.productorVariedades')
    </div>
    <div class="modal-footer">

        <button id="btn_enviar" type="submit" class="btn btn-primary btn_ok">Guardar</button>
    </div>

    <div class="modal-header">
        <h4 class="modal-title titulo_formulario" id="">Productores-Variedad</h4>
    </div>
    <div class="modal-body">
        @include('admin.productorVariedades.form.table')
    </div>
    </div>

@endsection
@section('js')
    <script type="text/javascript">
        $(document).ready(function () {

            $("#region_id").change(function () {
                var route = "{!!URL::to('/getProductoresByRegionId')!!}";
                var region_id = $("#region_id").val();
                var select = $("#productor_id");
                var token = $("input[name=_token]").val();
                $("#productor_id option").remove();
                $.post(route, {region_id: region_id, _token: token})
                    .done(function (data) {
                        $(data).each(function (index, value) {
                            select.append("<option value='" + value.id + "'> " + value.nombre + "</option>");
                            console.log(value.id + value.nombre);
                        });
                    });
            });

            $("#productor_id").change(function () {
                var route = "{!!URL::to('/getProductorVariedadByProductor')!!}";
                var productor_id = $("#productor_id").val();
                var token = $("input[name=_token]").val();
                //$("#productor_id option").remove();
                $.post(route, {productor_id: productor_id, _token: token})
                    .done(function (data) {
                        //alert(data);
                        $('#tablaDatos tbody').append(data);
                    });
            });

            $("#btn_enviar").click(function () {
                var route = "{!!URL::to('/setProductorVariedad')!!}";
                var region_id = $("#region_id").val();
                var productor_id = $("#productor_id").val();
                var variedad_id = $("#variedad_id").val();
                var select = $("#productor_id");
                var token = $("input[name=_token]").val();
                $.post(route, {
                    variedad_id: variedad_id,
                    productor_id: productor_id,
                    region_id: region_id,
                    _token: token
                })
                    .done(function (data) {
                        if (data != '') {
                            $('#tablaDatos tbody').append(data);
                        }
                    });
            });


            $(".boton").on("click",function () {

                alert("asd");
                var id_dato = $(this).attr("id");
                alert(id_dato);
                var route = "{!!URL::to('/setProductorVariedad')!!}";
                var region_id = $("#region_id").val();
                var productor_id = $("#productor_id").val();
                var variedad_id = $("#variedad_id").val();
                var select = $("#productor_id");
                var token = $("input[name=_token]").val();
                $.post(route, {variedad_id: variedad_id, productor_id: productor_id, region_id: region_id, _token: token})
                    .done(function (data) {
                        console.log(data);
                        $('#tablaDatos tbody').append(data);
                    });
            });




        });






    </script>
@endsection
