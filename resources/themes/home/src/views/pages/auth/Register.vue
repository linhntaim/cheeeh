<template lang="pug">
    .card
        .card-body
            h4.font-weight-bold.text-base-red.mb-3 Create an account
            form
                .form-group
                    input.form-control(type="text" placeholder="Email")
                .form-group
                    input.form-control(type="password" placeholder="Password")
                .form-group
                    input.form-control(type="password" placeholder="Confirm password")
                .form-group
                    button.btn.btn-base-red(type="submit") Register
            nav
                .mt-2
                    router-link.link-base-red(:to="{name: 'login'}") Already have an account? Login!
                .mt-2
                    router-link.link-base-red(:to="{name: 'forgot_password'}") Forgot password?
</template>

<script>
    export default {
        name: 'Register',
    }
</script>

<style scoped>

</style>
