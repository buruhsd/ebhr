<template>
    <div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('/media/bg/bg-3.jpg'); min-height: 100vh;">
        <div class="login-form text-center p-7 position-relative overflow-hidden">
            <div class="d-flex flex-center mb-15">
                <a href="#">
                    <img src="/media/logos/logo-letter-13.png" class="max-h-75px" alt="">
                </a>
            </div>
            <div class="login-signin">
                <div class="mb-10">
                    <h1 class="text-danger font-weight-boldest error-title mb-1">Oops!</h1>
                    <h5>Something went wrong here.</h5>
                    <div class="text-muted font-weight-bold">We're working on it and we'll get it fixedas soon possible.You can back or use our Help Center.</div>
                </div>
                <div class="mt-10">
                    <router-link :to="{ name: 'dashboard' }" class="btn btn-primary font-weight-bold px-9 py-4 my-3 mx-4">
                        <span>Home</span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        created () {
            document.title = this.$route.meta.title;
        }
    }
</script>
<style>
    .error-title{
        font-size: 5rem !important;
    }
</style>
