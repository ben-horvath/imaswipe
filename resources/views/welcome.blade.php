<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ env('APP_NAME') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        <script>
            var mediumPermalinkBase = '{{ route('welcome-start-with', ['name' => '']) . '/' }}';
            var firstMedium = '{{ !empty($first_medium) ? $first_medium : '' }}';
            var mode = 'browse';
            var viewer = '{{ Auth::guest() ? 'guest' : '' }}';
        </script>
    </head>
    <body class="welcome-body">
        <div id="app">
            <app></app>
        </div>

        @if(Auth::guest())
            <a href="login/google">
                <img class="login-button" src="interface/login-buttons/google-normal.png">
            </a>
        @else
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
    
            <div class="bottom-app-interface">
                <i
                    class="material-icons-outlined app-button"
                    onclick="alert('Coming soon');"
                >info</i>

                <i
                    class="material-icons-outlined app-button"
                    onclick="alert('Coming soon');"
                >pause</i>

                <i
                    class="material-icons-outlined app-button"
                    onclick="document.getElementById('logout-form').submit();"
                >exit_to_app</i>
            </div>
        @endif

        <!-- Scripts -->
        <script src="{{ asset('js/clipboard.min.js') }}"></script>
        <script src="{{ asset('js/hammer.min.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>