{{--
    Theme-aware tokens for the panel pages.

    Filament defines --gray-* once, as a fixed palette, and does dark mode with
    utility classes carrying a `.dark` selector. Inline styles cannot use those
    utilities, so a raw var(--gray-200) would stay light when the panel is dark.
    These four tokens flip with the theme and are what the views actually use.
--}}
@once
    <style>
        :root {
            --rag-muted: var(--gray-500);
            --rag-line: var(--gray-200);
            --rag-track: var(--gray-100);
            --rag-strong: var(--gray-950);
        }

        .dark {
            --rag-muted: var(--gray-400);
            --rag-line: var(--gray-700);
            --rag-track: var(--gray-800);
            --rag-strong: #fff;
        }
    </style>
@endonce
