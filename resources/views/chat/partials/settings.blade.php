{{--
    Everything that is not "type a question".

    The page is meant for someone who wants an answer, not someone tuning a
    retriever, so nothing here is on the main surface. Each field is behind its
    own ability, and the server drops any of them that this user may not set --
    see AskRequest::prepareForValidation().
--}}
<div class="rag-modal" id="rag-settings" role="dialog" aria-modal="true"
     aria-labelledby="rag-settings-title" hidden>
    <h2 id="rag-settings-title">{{ __('rag::rag.chat.settings') }}</h2>
    <p class="rag-modal__sub">{{ __('rag::rag.chat.settings_sub') }}</p>

    @if ($abilities['model'] && $payload['models'])
        <div class="rag-field">
            <label class="rag-field__label" for="rag-set-model">{{ __('rag::rag.chat.model') }}</label>
            <select id="rag-set-model">
                @foreach ($payload['models'] as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>
            <span class="rag-field__help">{{ __('rag::rag.chat.model_help') }}</span>
        </div>
    @endif

    @if ($abilities['sources'] && $payload['sources'])
        <div class="rag-field">
            <span class="rag-field__label">{{ __('rag::rag.chat.sources_field') }}</span>
            <div class="rag-checkboxes">
                @foreach ($payload['sources'] as $source)
                    <label class="rag-check">
                        <input type="checkbox" name="rag-source" value="{{ $source['key'] }}">
                        <span>{{ $source['label'] }}</span>
                    </label>
                @endforeach
            </div>
            <span class="rag-field__help">{{ __('rag::rag.chat.sources_help') }}</span>
        </div>
    @endif

    @if ($abilities['advanced'])
        <div class="rag-field">
            <label class="rag-field__label" for="rag-set-topk">
                {{ __('rag::rag.chat.top_k') }}
                <span class="rag-field__value" id="rag-set-topk-value"></span>
            </label>
            <input type="range" id="rag-set-topk" min="1" max="30" step="1">
            <span class="rag-field__help">{{ __('rag::rag.chat.top_k_help') }}</span>
        </div>

        <div class="rag-field">
            <label class="rag-field__label" for="rag-set-score">
                {{ __('rag::rag.chat.min_score') }}
                <span class="rag-field__value" id="rag-set-score-value"></span>
            </label>
            <input type="range" id="rag-set-score" min="0" max="1" step="0.01">
            <span class="rag-field__help">{{ __('rag::rag.chat.min_score_help') }}</span>
        </div>

        <div class="rag-field">
            <label class="rag-check">
                <input type="checkbox" id="rag-set-retrieval">
                <span>{{ __('rag::rag.chat.retrieval_only') }}</span>
            </label>
            <span class="rag-field__help">{{ __('rag::rag.chat.retrieval_only_help') }}</span>
        </div>
    @endif

    <div class="rag-modal__foot">
        <button type="button" class="rag-btn" id="rag-settings-reset">{{ __('rag::rag.chat.reset') }}</button>
        <button type="button" class="rag-btn" id="rag-settings-cancel">{{ __('rag::rag.chat.cancel') }}</button>
        <button type="button" class="rag-btn rag-btn--primary" id="rag-settings-save">{{ __('rag::rag.chat.save') }}</button>
    </div>
</div>
