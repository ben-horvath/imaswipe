<template>
    <div id="medium-container" @click="requestStepMedia">
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
    // Import helpers
    import { exists } from '../helpers.js';

    // Import Vue components
    import ImageComp from './Image.vue';
    import VideoComp from './Video.vue';

    // Import media classes
    import {
        Medium as MediumClass,
        Image as ImageClass,
        Video as VideoClass
    } from '../gallery.js';

    export default {
        data() {
            return {
                mediaBufferSize: 2, // TODO: get value from config
                media: [],
                loadingMedium: false,
                needMoreMedia: true,
                stepMediaRequested: false
            }
        },
        mounted() {
            new ClipboardJS('#permalink');

            window.oncontextmenu = function(event) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('permalink').style.display = 'initial';
                return false;
            };

            this.conditionalAddMediumToBuffer();
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
            addMediumToBuffer() {
                this.loadingMedium = true;

                axios.get('/api/media/random')
                    .then((response) => {
                        /* set new medium resource on success */

                        if (
                            !exists(response.data) ||
                            !exists(response.data.data)
                        ) {
                            console.log('Unexpected response from server.'); // TODO: error handling
                        }

                        let relevantData = [
                            response.data.data.id,
                            response.data.data.name,
                            response.data.data.extension,
                            response.data.data.mime_type,
                            response.data.data.url
                        ];
                        
                        let type = MediumClass.getType(response.data.data.mime_type);
                        
                        if (typeof type !== 'string') {
                            console.log('Not supported MIME type: ' + typeof type); // TODO: error handling
                            return;
                        }

                        switch (type) {
                            case 'image':
                                this.media.push(new ImageClass(...relevantData));
                                break;
                        
                            case 'video':
                                this.media.push(new VideoClass(...relevantData));
                                break;
                        
                            default:
                                console.log('Unsupported medium type: ' + type); // TODO: error handling
                                return;
                                break;
                        }
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
                if (!this.loadingMedium && this.needMoreMedia) {
                    this.addMediumToBuffer();
                }
            },
            requestStepMedia() {
                this.stepMediaRequested = true;
            },
            conditionalStepMedia() {
                if (
                    this.media.length > 1 &&
                    this.stepMediaRequested
                ) {
                    this.media.shift();
                    this.stepMediaRequested = false;
                }
            }
        },
        components: {
            imagecomp: ImageComp,
            videocomp: VideoComp
        }
    }
</script>
