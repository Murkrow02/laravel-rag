<div class="rag-empty" id="rag-empty" @if ($hasMessages) hidden @endif>
    <h1>{{ __('rag::rag.chat.empty_title') }}</h1>
    <p>{{ __('rag::rag.chat.empty_body') }}</p>

    @if ($payload['suggestions'])
        <div class="rag-suggestions">
            @foreach ($payload['suggestions'] as $suggestion)
                <button type="button" class="rag-suggestion" data-prompt="{{ $suggestion }}">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    @endif
</div>
