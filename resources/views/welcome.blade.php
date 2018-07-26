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
            let loadingNextMedium = false;

            function preloadNextMedium() {
                loadingNextMedium = true;

                axios.get('/api/media/random')
                    .then(function (response) {
                        /* set new medium source on success */
                        nextMedium = response.data.data;

                        createNextMediumElement();
                    })
                    .catch(function (error) {
                        /* handle errors */
                        console.log(error);
                    })
                    .then(function () {
                        /* always executed */
                        loadingNextMedium = false;
                    });
            }

            function stepMedia() {
                mediumElement = document.getElementById('medium');
                mediumContainer = document.getElementById('medium-container');
                mediumContainer.replaceChild(nextMediumElement, mediumElement);

                if (!loadingNextMedium) {
                    preloadNextMedium();
                }
            }

            function createNextMediumElement() {
                if (nextMedium.mime_type.startsWith('image/')) {
                    nextMediumElement = document.createElement("img");
                } else if (nextMedium.mime_type.startsWith('video/')) {
                    nextMediumElement = document.createElement("video");
                    nextMediumElement.setAttribute('autoplay', '');
                    nextMediumElement.setAttribute('loop', '');
                    nextMediumElement.setAttribute('muted', '');
                }
                nextMediumElement.setAttribute('src', nextMedium.url);
                nextMediumElement.classList.add('max-full-height');
                nextMediumElement.classList.add('max-full-width');
                nextMediumElement.id = 'medium';
            }
        </script>
    </head>
    <body>
        <div
            id="medium-container"
            onclick="stepMedia()"
            class="flex-center position-ref full-height full-width"
        >
            @if(substr_count($initial_medium['mime_type'], 'image/'))
                <img
                    id="medium"
                    class="max-full-height max-full-width"
                    src="{{ $initial_medium['url'] }}"
                >
            @elseif(substr_count($initial_medium['mime_type'], 'video/'))
                <video
                    id="medium"
                    class="max-full-height max-full-width"
                    src="{{ $initial_medium['url'] }}"
                    autoplay
                    loop
                    muted>
                </video>
            @endif
        </div>

        <script>
            nextMedium = {!! json_encode($next_medium) !!}

            createNextMediumElement();
        </script>
    </body>
</html>