<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Assess - {{ env('APP_NAME') }}</title>

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
        <script src="js/hammer.min.js"></script>

        <script>
            /* init media */
            @if(!empty($initial_medium))
                currentMedium = {!! json_encode($initial_medium) !!};
            @endif
            @if(!empty($next_medium))
                nextMedium = {!! json_encode($next_medium) !!};
            @endif

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

                axios.get('/api/media/assess')
                    .then(function (response) {
                        /* set new medium source on success */
                        if (typeof response.data == 'object' &&
                            typeof response.data.data == 'object'
                        ) {
                            nextMedium = response.data.data;
                            createNextMediumElement();
                        } else {
                            nextMedium = null;
                        }
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
                if (typeof nextMedium === 'object' &&
                    nextMedium !== null
                ) {
                    mediumElement = document.getElementById('medium');
                    mediumContainer.replaceChild(nextMediumElement, mediumElement);
                    currentMedium = Object.assign({}, nextMedium);

                    if (!loadingNextMedium) {
                        preloadNextMedium();
                    }
                } else {
                    mediumContainer.removeChild(mediumElement);
                    currentMedium = null;
                    mediumElement = null;
                }
            }

            function createNextMediumElement() {
                if (nextMedium.mime_type.startsWith('image/')) {
                    nextMediumElement = document.createElement("img");
                } else if (nextMedium.mime_type.startsWith('video/')) {
                    nextMediumElement = document.createElement("video");
                    nextMediumElement.autoplay = true;
                    nextMediumElement.loop = true;
                    nextMediumElement.muted = true;
                }
                nextMediumElement.src = nextMedium.url;
                nextMediumElement.classList.add('max-full-height');
                nextMediumElement.classList.add('max-full-width');
                nextMediumElement.id = 'medium';
            }
        </script>
    </head>
    <body>
        <div
            id="medium-container"
            class="flex-center position-ref full-height full-width"
        >
            @if(!empty($initial_medium))
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
            @endif
        </div>

        <script>
            mediumContainer = document.getElementById('medium-container');

            @if(!empty($initial_medium))
                mediumElement = document.getElementById('medium');
            @else
                mediumElement = null;
            @endif

            @if(!empty($next_medium))
                createNextMediumElement();
            @else
                nextMediumElement = null;
            @endif

            /* init Hammer */
            hammer = new Hammer(mediumContainer);

            /* hammer event listeners */
            hammer.on('swipeleft', function(ev) {
                if (mediumElement) {
                    axios.delete('/api/media/' + currentMedium.id);
                    stepMedia();
                }
            });

            hammer.on('swiperight', function(ev) {
                if (mediumElement) {
                    axios.patch('/api/media/' + currentMedium.id, {approved: true});
                    stepMedia();
                }
            });

            hammer.on('tap', function(ev) {
                if (mediumElement) {
                    stepMedia();
                }
            });
        </script>
    </body>
</html>