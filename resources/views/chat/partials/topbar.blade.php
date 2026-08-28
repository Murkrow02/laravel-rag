<header class="rag-topbar">
    <button type="button" class="rag-icon-btn" id="rag-sidebar-toggle"
            title="{{ __('rag::rag.chat.toggle_sidebar') }}"
            aria-label="{{ __('rag::rag.chat.toggle_sidebar') }}">
        @include('rag::chat.partials.icon', ['name' => 'menu'])
    </button>

    <span class="rag-topbar__title" id="rag-title">{{ $payload['current']['title'] ?? __('rag::rag.chat.new_chat') }}</span>

    @if ($showSettings)
        <button type="button" class="rag-icon-btn" id="rag-settings-open"
                title="{{ __('rag::rag.chat.settings') }}"
                aria-label="{{ __('rag::rag.chat.settings') }}">
            @include('rag::chat.partials.icon', ['name' => 'gear'])
        </button>
    @endif
</header>
