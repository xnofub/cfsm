@php
    $disabled = $disabled ?? true;
    $class = $disabled ? 'disabled' : '';
    $regiones = $regiones ?? [];
    $productores = $productores ?? [];
@endphp

<div class="row">

    @if($disabled)
        <div class="col-lg-4">
            {!! Form::label('region_id', 'Región', array('class' => 'col-lg-3')) !!}
            <div class="col-lg-12">
                {!! Form::select('region_id', $regiones, isset($region->region_id) ? $region->region_id : '' , array('class' => 'form-control col-lg-9' , 'id'=>'region_id')) !!}
            </div>
        </div>
        <div class="col-lg-4">
            {!! Form::label('productor_id', 'Productor', array('class' => 'col-lg-3')) !!}
            <div class="col-lg-12">
                {!! Form::select('productor_id', $productores, isset($productor->productor_id) ? $productor->productor_id : '' , array('class' => 'form-control col-lg-9' , 'id'=>'productor_id')) !!}
            </div>
        </div>
    @endif
    <div class="col-lg-4">
        {!! Form::label('variedad_id', 'Variedad', array('class' => 'col-lg-3')) !!}
        <div class="col-lg-12">
            <select class="form-control col-lg-9" id="variedad_id">
                @foreach ($variedades as $variedad )
                    <option value="{{ $variedad['id'] }}">{{ $variedad['name'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

</div>


