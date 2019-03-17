<template>
    <video
        id="medium"
        :src="src"
        autoplay
        loop
        muted>
    </video>
</template>

<script>
    export default {
        props: ['src']
    }
</script>
