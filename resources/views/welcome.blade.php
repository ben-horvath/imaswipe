<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

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

        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.min.js"></script>

        <script>
            /* init axios */
            axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

            let token = document.head.querySelector('meta[name="csrf-token"]');

            if (token) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
            } else {
                console.error('CSRF token not found');
            }

            /* preload and step media */
            let nextMedium = new Image();

            function preloadNextMedium() {
                axios.get('/api/media/random')
                    .then(function (response) {
                        console.log(response);
                        nextMedium.src = response.data.data.url;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }

            function stepMedia() {
                document.getElementById('medium').src = nextMedium.src;

                preloadNextMedium();
            }
        </script>
    </head>
    <body>
        <div
            onclick="stepMedia()"
            class="flex-center position-ref full-height full-width"
        >
            <img
                id="medium"
                class="max-full-height max-full-width"
                src="{{ $initial_medium['url'] }}"
            >
        </div>

        <script>
            nextMedium.src = "{{ $next_medium['url'] }}"
        </script>
    </body>
</html>