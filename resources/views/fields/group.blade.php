<div class="{{$groupClass}} {{ $align }}">
    @foreach($group as $field)
        <div class="{{ $class }} @if (!$loop->last) pe-0 @endif">
            {!! $field !!}
        </div>
    @endforeach
</div>
