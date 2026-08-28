{{--
    The sources panel.

    This is the point of the page as much as the answer is: an answer nobody
    can check against the corpus is a claim, not a citation. It stays closed
    until a [#n] marker or the "sources" chip asks for it, and its contents are
    rendered by rag-chat.js from whatever the answer actually retrieved.
--}}
@if ($abilities['passages'])
    <aside class="rag-drawer" id="rag-drawer" data-open="false" aria-label="{{ __('rag::rag.chat.sources') }}">
        <div class="rag-drawer__head">
            <span class="rag-drawer__title">{{ __('rag::rag.chat.sources') }}</span>
            <span class="rag-drawer__count" id="rag-drawer-count"></span>

            <button type="button" class="rag-icon-btn" id="rag-drawer-close"
                    aria-label="{{ __('rag::rag.chat.close') }}">
                @include('rag::chat.partials.icon', ['name' => 'close'])
            </button>
        </div>

        <div class="rag-drawer__body" id="rag-drawer-body"></div>
    </aside>
@endif
