<template>
    <div class="container media-group">
        <div class="row reference">
            <div class="col-4">
                <component :is="comp" :src="'storage/' + mediagroup.name + '.' + mediagroup.extension"></component>
            </div>

            <div class="col-8 d-flex flex-column justify-content-end">
                <button class="btn btn-primary" @click="merge">Merge duplicates</button>
            </div>
        </div>

        <div class="row duplicates">
            <div v-for="duplicate in duplicates" :key="duplicate.name" class="col-4">
                <component :is="comp" :src="'storage/' + duplicate.name + '.' + duplicate.extension"></component>
            </div>
        </div>
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
        props: [
            'mediagroup'
        ],

        data() {
            return {
                duplicates: [],
                comp: ''
            }
        },

        created() {
            let type = MediumClass.getType(this.mediagroup.mimeType);
            
            switch (type) {
                case 'image':
                    this.comp = 'imagecomp';
                    break;
            
                case 'video':
                    this.comp = 'videocomp';
                    break;
            
                default:
                    // TODO: error handling
                    break;
            }
        },

        mounted() {
            this.reload();
        },

        watch: {
            mediagroup() {
                this.reload();
            }
        },

        methods: {
            reload() {
                this.duplicates.length = 0;
                this.mediagroup.duplicates.forEach(medium => {
                    let relevantData = [
                        null,
                        medium.name,
                        medium.extension,
                        this.mediagroup.mimeType
                    ];

                    switch (this.comp) {
                        case 'imagecomp':
                            this.duplicates.push(new ImageClass(...relevantData));
                            this.duplicates[this.duplicates.length - 1].load();
                            break;
                    
                        case 'videocomp':
                            this.duplicates.push(new VideoClass(...relevantData));
                            this.duplicates[this.duplicates.length - 1].load();
                            break;
                    
                        default:
                            console.log('Unsupported component: ' + this.comp); // TODO: error handling
                            break;
                    }
                });
            },

            merge() {
                let requestPayload = {
                    reference: this.mediagroup.name,
                    duplicates: []
                };

                this.duplicates.forEach(duplicate => {
                    requestPayload.duplicates.push(duplicate.name);
                });

                axios
                    .post('/api/media/merge', requestPayload)
                    .then(response => {
                        //show success note
                    })
                    .catch(response => {
                        console.log(response); // TODO: error handling
                    })
                    .then(() => {
                        // Always executed
                        this.$emit('merge');
                    })
                ;
            }
        },
        
        components: {
            imagecomp: ImageComp,
            videocomp: VideoComp
        }
    }
</script>
