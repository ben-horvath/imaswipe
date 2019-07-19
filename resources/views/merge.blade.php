<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Merge - {{ env('APP_NAME') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        <script>
            window.mediaGroups = @json($media_groups);
            if (!Array.isArray(window.mediaGroups)) {
                window.mediaGroups = [];
            }
        </script>
    </head>
    <body>
        <div id="merge">
            <merge></merge>
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/clipboard.min.js') }}"></script>
        <script src="{{ asset('js/hammer.min.js') }}"></script>
        <script src="{{ mix('js/merge.js') }}"></script>
    </body>
</html>