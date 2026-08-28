{{--
    A complete HTML document, on purpose.

    The package cannot assume the host has a layout to extend: this one does
    not, and Laravel's own default points at a view that is not there. Anything
    the page needs, it brings.

    Point rag.chat.layout at one of your own views to replace this. It has to
    yield three sections: rag-head, rag-content and rag-scripts.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('rag-title', __('rag::rag.chat.title'))</title>

    @yield('rag-head')
</head>
<body class="rag-body">
@yield('rag-content')
@yield('rag-scripts')
</body>
</html>
