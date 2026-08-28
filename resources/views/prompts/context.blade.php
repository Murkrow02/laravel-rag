{{--
    One block per retrieved chunk. The marker, the provenance label and the
    text are all the model needs to cite accurately.
--}}
@foreach ($citations as $citation)
[#{{ $citation->marker }}] {{ $citation->label }}
{{ $citation->chunk->content }}

@endforeach
