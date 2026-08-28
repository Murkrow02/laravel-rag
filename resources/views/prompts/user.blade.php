@if (! empty($history))
Conversation so far:
@foreach ($history as $turn)
{{ $turn['role'] === 'user' ? 'User' : 'Assistant' }}: {{ $turn['content'] }}
@endforeach

@endif
Context blocks:

{{ $context }}

Question: {{ $question }}
