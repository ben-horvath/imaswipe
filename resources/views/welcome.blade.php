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
            <div class="login-section">
                <a href="login/google">
                    <img src="interface/login-buttons/google-normal.png">
                </a>
                @if(
                    !empty(__('welcome.sign-in-note')) &&
                    __('welcome.sign-in-note') != 'welcome.sign-in-note'
                )
                    <p>@lang('welcome.sign-in-note')</p>
                @endif
            </div>
        @else
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
    
            <div class="bottom-app-interface">
                <i
                    class="material-icons-outlined app-button"
                    data-toggle="modal"
                    data-target="#infoModal"
                >info</i>

                <i
                    class="material-icons-outlined app-button"
                    data-toggle="modal"
                    data-target="#pauseModal"
                >pause</i>

                <i
                    class="material-icons-outlined app-button"
                    onclick="document.getElementById('logout-form').submit();"
                >exit_to_app</i>
            </div>

            @component('components.modal')
                @slot('id')
                    infoModal
                @endslot

                @slot('title')
                    {{ env('APP_NAME') }}
                @endslot

                @slot('body')
                    @lang('welcome.info-modal-body')
                @endslot

                @slot('primaryButton')
                    @lang('welcome.info-modal-button')
                @endslot
            @endcomponent

            @component('components.modal')
                @slot('id')
                    pauseModal
                @endslot

                @slot('title')
                    @lang('welcome.pause-modal-title')
                @endslot

                @slot('body')
                    @lang('welcome.pause-modal-body')
                @endslot

                @slot('primaryButton')
                    @lang('welcome.pause-modal-primary-button')
                @endslot

                @slot('primaryButtonAction')
                    upgrade
                @endslot

                @slot('secondaryButton')
                    @lang('welcome.pause-modal-secondary-button')
                @endslot
            @endcomponent

            @component('components.modal')
                @slot('id')
                    upgradeThanksModal
                @endslot

                @slot('title')
                    @lang('welcome.upgrade-thanks-modal-title')
                @endslot

                @slot('body')
                    @lang('welcome.upgrade-thanks-modal-body')
                @endslot

                @slot('primaryButton')
                    @lang('welcome.upgrade-thanks-modal-button')
                @endslot
            @endcomponent
        @endif

        <!-- Scripts -->
        <script src="{{ asset('js/clipboard.min.js') }}"></script>
        <script src="{{ asset('js/hammer.min.js') }}"></script>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>