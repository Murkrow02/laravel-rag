<div class="rag-composer">
    <div class="rag-composer__inner">
        {{-- What the next question will run with. Populated by rag-chat.js so
             it stays in step with the settings modal. --}}
        <div class="rag-pills" id="rag-pills"></div>

        <div class="rag-box">
            <textarea class="rag-input" id="rag-input" rows="1" autocomplete="off"
                      placeholder="{{ __('rag::rag.chat.placeholder') }}"
                      aria-label="{{ __('rag::rag.chat.placeholder') }}"></textarea>

            <button type="button" class="rag-send" id="rag-send" aria-label="{{ __('rag::rag.chat.send') }}">
                @include('rag::chat.partials.icon', ['name' => 'send'])
            </button>
        </div>

        <p class="rag-hint">{{ __('rag::rag.chat.hint') }}</p>
    </div>
</div>
