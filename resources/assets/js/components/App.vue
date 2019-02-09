<template>
    <div id="medium-container">
        <component v-if="media.length" :is="comp" :src="src"></component>

        <button
            id="permalink"
            class="permalink"
            onclick="this.style.display = 'none';"
            :data-clipboard-text="clipboardText"
        >Copy Link</button>
    </div>
</template>

<script>
    // Import Vue components
    import ImageComp from './Image.vue';
    import VideoComp from './Video.vue';

    // Import media classes
    import {
        Medium as MediumClass,
        ImageMedium as ImageClass,
        VideoMedium as VideoClass
    } from '../gallery.js';

    export default {
        data() {
            return {
                mediaBufferSize: 5, // TODO: get value from config
                media: [],
                loadingMedium: false,
                needMoreMedia: true,
                stepMediaRequested: false,
                noMoreMediaAvailable: false
            }
        },
        mounted() {
            /* Init ClipboardJS */
            new ClipboardJS('#permalink');

            window.oncontextmenu = function(event) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('permalink').style.display = 'initial';
                return false;
            };

            /* Init Hammer */
            let hammerElement = document.getElementById('medium-container');
            window.hammer = new Hammer(hammerElement);
            PreventGhostClick(hammerElement);

            /* Register Hammer actions */
            window.hammer.on('swipeleft', (event) => {
                if (this.media.length) {
                    if (window.hasOwnProperty('assess') && assess === true) {
                        axios.delete('/api/media/' + this.media[0].name)
                            .then((response) => {
                                this.fillMediaBuffer(response);
                            })
                            .catch((error) => {
                                /* TODO: handle errors if failed */
                                console.log(error); // TODO: error handling
                            })
                            .then(() => {
                                /* always executed */
                                this.loadingMedium = false;
                            });
                    }
                    this.requestStepMedia();
                }
            });

            window.hammer.on('swiperight', (event) => {
                if (this.media.length) {
                    if (window.hasOwnProperty('assess') && assess === true) {
                        axios.patch('/api/media/' + this.media[0].name, {approved: true})
                            .then((response) => {
                                this.fillMediaBuffer(response);
                            })
                            .catch((error) => {
                                /* TODO: handle errors if failed */
                                console.log(error); // TODO: error handling
                            })
                            .then(() => {
                                /* always executed */
                                this.loadingMedium = false;
                            });
                    }
                    this.requestStepMedia();
                }
            });

            window.hammer.on('tap', (event) => {
                this.requestStepMedia();
            });

            if (typeof firstMedium === 'string' && firstMedium.length) {
                this.addMediumToBuffer(firstMedium);
            } else {
                this.addMediumToBuffer();
            }
        },
        created() {
            this.throttledAddMediumToBuffer = _.throttle(this.addMediumToBuffer, 2000)
        },
        computed: {
            clipboardText() {
                if (this.media.length) {
                    return mediumPermalinkBase + this.media[0].name;
                }
            },
            comp() {
                if (this.media.length) {
                    if (this.media[0] instanceof VideoClass) {
                        return 'videocomp';
                    }
                }
                return 'imagecomp';
            },
            src() {
                if (this.media.length) {
                    return this.media[0].src;
                } else {
                    return '';
                }
            }
        },
        watch: {
            loadingMedium() {
                this.conditionalAddMediumToBuffer();
            },
            needMoreMedia() {
                this.conditionalAddMediumToBuffer();
            },
            media() {
                if (this.media.length < this.mediaBufferSize) {
                    this.needMoreMedia = true;
                } else {
                    this.needMoreMedia = false;
                }

                this.conditionalStepMedia();
            },
            stepMediaRequested() {
                this.conditionalStepMedia();
            }
        },
        methods: {
            addMediumToBuffer(medium = null) {
                this.loadingMedium = true;

                let requestPath = '/api/media/random';
                if (typeof medium === 'string' && medium.length) {
                    requestPath = '/api/media/' + medium;
                }
                if (window.hasOwnProperty('assess') && assess === true) {
                    requestPath = '/api/media/assess';
                }
                axios.get(requestPath)
                    .then((response) => {
                        this.fillMediaBuffer(response);
                    })
                    .catch((error) => {
                        /* TODO: handle errors if failed */
                        console.log(error); // TODO: error handling
                    })
                    .then(() => {
                        /* always executed */
                        this.loadingMedium = false;
                    });
            },
            conditionalAddMediumToBuffer() {
                if (
                    !this.loadingMedium &&
                    this.needMoreMedia &&
                    !this.noMoreMediaAvailable
                ) {
                    this.throttledAddMediumToBuffer();
                }
            },
            requestStepMedia() {
                this.stepMediaRequested = true;
            },
            conditionalStepMedia() {
                let minMediaCount = 1;

                if (window.hasOwnProperty('assess') && assess === true) {
                    minMediaCount = 0;
                }

                if (
                    this.media.length > minMediaCount &&
                    this.stepMediaRequested
                ) {
                    this.media.shift();
                    this.stepMediaRequested = false;
                }
            },
            fillMediaBuffer(response) {
                if (
                    !response.hasOwnProperty('data') ||
                    !response.data.hasOwnProperty('data')
                ) {
                    if (window.hasOwnProperty('assess') && assess === true) {
                        this.noMoreMediaAvailable = true;
                        console.log('Reached end of media.'); // TODO: notify user
                    } else {
                        console.log('Unexpected response from server.'); // TODO: error handling
                    }
                    return;
                }

                walkThroughNew:
                for (
                    let i = 0;
                    this.media.length < this.mediaBufferSize &&
                    i < response.data.data.length;
                    i++
                ) {
                    walkThroughExisting:
                    for (let j = 0; j < this.media.length; j++) {
                        if (this.media[j].name == response.data.data[i].name) {
                            continue walkThroughNew;
                        }
                    }

                    let relevantData = [
                        response.data.data[i].id,
                        response.data.data[i].name,
                        response.data.data[i].extension,
                        response.data.data[i].mime_type,
                        response.data.data[i].url
                    ];
                    
                    let type = MediumClass.getType(response.data.data[i].mime_type);
                    
                    if (typeof type !== 'string') {
                        console.log('Not supported MIME type: ' + typeof type); // TODO: error handling
                        return;
                    }

                    switch (type) {
                        case 'image':
                            this.media.push(new ImageClass(...relevantData));
                            this.media[this.media.length - 1].load();
                            break;
                    
                        case 'video':
                            this.media.push(new VideoClass(...relevantData));
                            this.media[this.media.length - 1].load();
                            break;
                    
                        default:
                            console.log('Unsupported medium type: ' + type); // TODO: error handling
                            return;
                            break;
                    }
                }
            }
        },
        components: {
            imagecomp: ImageComp,
            videocomp: VideoComp
        }
    }
</script>
