<template lang="pug">
    div
        router-link(:to="{name: 'logout'}") Logout
</template>

<script>
    export default {
        name: 'Home',
    }
</script>

<style scoped>

</style>
