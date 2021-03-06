@extends('layouts.web')
@section('title', 'Productor')
@section('content')


@include('layouts.error')

{!! Form::open(['route' => 'productores.store', 'method' => 'POST', 'class' => 'form-horizontal crear','role'=>'form']) !!}
<div class="modal-header">
    <h4 class="modal-title titulo_formulario" id="">AGREGAR JUGADOR</h4>
</div>
<div class="modal-body">    
    @include('admin.productores.form.productor')
</div>
<div class="modal-footer">
    
    <button type="submit" class="btn btn-primary btn_ok">Enviar</button>
</div>
{!! Form::close() !!}

@endsection
@section('js')
<script type="text/javascript">
    $(document).ready(function () {
        $('.datepicker').datepicker({
            startDate: '-3d'
        });
    });
</script>
@endsection