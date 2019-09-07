<template lang="pug">
    .card
        .card-body
            form
                .form-group
                    input.form-control(type="text" placeholder="Email")
                .form-group
                    input.form-control(type="password" placeholder="Password")
                .form-group
                    button.btn.btn-base-pink(type="submit") Login
            nav
                .mt-2
                    router-link.link-base-pink(:to="{name: 'forgot_password'}") Forgot password?
                .mt-2
                    router-link.link-base-pink(:to="{name: 'register'}") Don't have an account? Create one!
</template>

<script>
    export default {
        name: 'Login',
    }
</script>

<style scoped>

</style>
