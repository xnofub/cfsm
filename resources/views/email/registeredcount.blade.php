<div>
    <h3>{{$data['body']}}</h3>
    @foreach($urls as $item)
        <li><a href="{{$item['url']}}"><b>{{$item['pallet']}}</b> : {{$item['description']}}</a></li>
    @endforeach
</div>
