<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ env('APP_NAME') }}</title>

        <!-- Fonts -->

        <!-- Styles -->
        <style>
            html, body {
                background-color: #000;
                height: 100%;
                margin: 0;
            }

            .full-height {
                height: 100%;
            }

            .full-width {
                width: 100%;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .max-full-height {
                max-height: 100%;
            }

            .max-full-width {
                max-width: 100%;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height full-width">
            <img
                class="max-full-height max-full-width"
                src="{{ asset('storage/OXHPI6MpqdbrI2pcFer7dveCW3pzX9RvMpAhj9Hz.jpeg') }}"
            >
        </div>
    </body>
</html>