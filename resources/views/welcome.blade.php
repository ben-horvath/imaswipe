<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        <script>
            var mediumPermalinkBase = '{{ route('welcome-start-with', ['name' => '']) . '/' }}';
            var firstMedium = '{{ !empty($first_medium) ? $first_medium : '' }}';
            var mode = 'browse';
        </script>
    </head>
    <body>
        <div id="app">
            <app></app>
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/clipboard.min.js') }}"></script>
        <script src="{{ asset('js/hammer.min.js') }}"></script>
        <script src="{{ asset('js/prevent-ghost-click.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>