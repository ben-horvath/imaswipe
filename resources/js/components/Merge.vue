<template>
    <div>
        <mediagroup v-if="this.mediaGroups.length" :mediagroup="mediaGroups[currentGroup]" @merge="removeGroup"></mediagroup>

        <p v-if="this.mediaGroups.length == 0">No duplicates found.</p>
    </div>
</template>

<script>
    // Import Vue components
    import MediaGroup from './MediaGroup.vue';

    export default {
        data() {
            return {
                mediaGroups: window.mediaGroups,
                currentGroup: 0
            }
        },

        mounted() {
            /* Init Hammer */
            let hammerElement = document.getElementById('merge');
            window.hammer = new Hammer(hammerElement);

            /* Register Hammer actions */
            window.hammer.on('swipeleft', (event) => {
                this.stepForward();
            });

            window.hammer.on('swiperight', (event) => {
                this.stepBackward();
            });
        },

        methods: {
            stepForward() {
                if (this.mediaGroups.length > (this.currentGroup + 1)) {
                    this.currentGroup++;
                }
            },

            stepBackward() {
                if (this.currentGroup > 0) {
                    this.currentGroup--;
                }
            },

            removeGroup() {
                this.mediaGroups.splice(this.currentGroup, 1);
                if (
                    this.currentGroup >= (this.mediaGroups.length - 1) &&
                    this.currentGroup > 0
                ) {
                    this.currentGroup--;
                }
            }
        },

        components: {
            mediagroup: MediaGroup
        }
    }
</script>
