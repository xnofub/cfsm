
<style>
table {
  width: 100%;
  border: 0px solid black;
}

th {
  background-color: #4CAF50;
  color: white;
}
th, td {
  padding: 15px;
  text-align: left;
  border: 0px solid black;
}
tr:nth-child(even) {background-color: #f2f2f2;}
</style>


<p>Productor :{{$productor->productor_nombre}}</p>
<p>Región :{{$productor->region->region_nombre}}</p>
<p>Fecha :{{$fecha}}</p>

<table>
        <tr>
                <th> Pallet</th>
                <th> Nota FInal</th>
        </tr>
        @foreach($pallets as $p)
            <tr>
                    <td> {{ $p->lote_codigo}}</td>
                    <td> {{ $p->nota_nombre}}</td>
            </tr>
        @endforeach
</table>

<br>

<table>
    <thead>
        <tr>
            <th>QR</th>
            <th>Pallet</th>
            <th>Variedad</th>
            <th>Categoria</th>
            <th>Embalaje</th>
            <th>Nota Muestra</th>
            <th>Apariencia</th>
            <th>Valor Maximo</th>
            <th>Defecto</th>
        </tr>
    </thead>
    @foreach($pallets as $m)
        <tr>
            <td>{{$m->muestra_qr ?? 'Sin ingresar'}}</td>
            <td>{{$m->lote_codigo ?? 'Sin ingresar'}}</td>
            <td>{{$m->variedad_nombre}}</td>
            <td>{{$m->categoria_nombre}}</td>
            <td>{{$m->embalaje_nombre}}</td>
            <td>{{$m->nota_nombre}}</td>
            <td>{{$m->apariencia_nombre}}</td>
            <td>{{$m->valor_maximo}}</td>
            <td>{{$m->defecto}}</td>
        </tr>
    @endforeach
</table>

<br>

<br>

<table>
    <thead>
        <tr>
            <th>QR</th>
            <th>Pallet</th>
            <th>Variedad</th>
            <th>Categoria</th>
            <th>Embalaje</th>
            <th>Nota Muestra</th>
            <th>Apariencia</th>
        </tr>
    </thead>
    @foreach($muestras as $m)
        <tr>
            <td>{{$m->muestra_qr ?? 'Sin ingresar'}}</td>
            <td>{{$m->lote_codigo ?? 'Sin ingresar'}}</td>
            <td>{{$m->variedad->variedad_nombre}}</td>
            <td>{{$m->categoria->categoria_nombre}}</td>
            <td>{{$m->embalaje->embalaje_nombre}}</td>
            <td>{{$m->nota->nota_nombre}}</td>
            <td>{{$m->apariencia->apariencia_nombre}}</td>
        </tr>
    @endforeach
</table>


