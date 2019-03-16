<template>
    <div id="medium-container">
        <component v-if="src" :is="comp" :src="src"></component>

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
        VideoMedium as VideoClass,
        MediaBuffer as MediaBuffer
    } from '../gallery.js';

    var startWith = null;
    if (typeof firstMedium === 'string' && firstMedium.length) {
        startWith = firstMedium;
    }

    export default {
        data() {
            return {
                mediaBuffer: new MediaBuffer(mode, startWith)
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
            window.hammer.on('tap', (event) => {
                this.mediaBuffer.stepMedia();
            });

            window.hammer.on('swipeleft', (event) => {
                this.mediaBuffer.deleteMedium();
            });

            window.hammer.on('swiperight', (event) => {
                this.mediaBuffer.approveMedium();
            });
        },
        computed: {
            clipboardText() {
                if (this.mediaBuffer.medium instanceof MediumClass) {
                    return mediumPermalinkBase + this.mediaBuffer.medium.name;
                }
            },
            comp() {
                if (this.mediaBuffer.medium instanceof VideoClass) {
                    return 'videocomp';
                } else {
                    return 'imagecomp';
                }
            },
            src() {
                if (this.mediaBuffer.medium instanceof MediumClass) {
                    return this.mediaBuffer.medium.src;
                } else {
                    return '';
                }
            }
        },
        methods: {
        },
        components: {
            imagecomp: ImageComp,
            videocomp: VideoComp
        }
    }
</script>
