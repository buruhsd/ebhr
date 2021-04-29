<template>
    <div class="login login-4 wizard d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="login-container order-2 order-lg-1 d-flex flex-center flex-row-fluid px-7 pt-lg-0 pb-lg-0 pt-4 pb-6 bg-white">
            <div class="login-content d-flex flex-column pt-lg-0 pt-12">
                <a href="#" class="login-logo pb-xl-20 pb-15">
                    <img v-bind:src="'/media/logos/logo-4.png'" class="max-h-70px" alt="" />
                </a>
                <form autocomplete="off" @submit.prevent="login" method="post">
                    <div class="login-form">
                        <div class="pb-5 pb-lg-15">
                            <h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Sign In</h3>
                        </div>
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark">Your Email</label>
                            <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg border-0" type="email" name="email" v-model="email" autocomplete="off" />
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between mt-n5">
                                <label class="font-size-h6 font-weight-bolder text-dark pt-5">Your Password</label>
                            </div>
                            <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg border-0" type="password" name="password" v-model="password" required autocomplete="current-password" />
                        </div>
                        <div class="pb-lg-0 pb-5">
                            <button type="submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="login-aside order-1 order-lg-2 bgi-no-repeat bgi-position-x-right">
            <div class="login-conteiner bgi-no-repeat bgi-position-x-right bgi-position-y-bottom" style="background-image: url(/media/svg/illustrations/login-visual-4.svg);">
                <h3 class="pt-lg-40 pl-lg-20 pb-lg-0 pl-10 py-20 m-0 d-flex justify-content-lg-start font-weight-boldest display5 display1-lg text-white">We Got
                <br />A Surprise
                <br />For You</h3>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                email: null,
                password: null,
                has_error: false
            }
        },
        methods: {
            login() {
                this.$store
                    .dispatch('login', {
                        email: this.email,
                        password: this.password
                    })
                    .then(() => {
                        this.$router.push({ name: 'About' })
                    })
                    .catch(err => {
                        console.log(err)
                    })
            }
        },
        created () {
            document.title = this.$route.meta.title;
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
<style>
    @media (min-width: 992px) {
        .login-container {
            min-height: 100vh;
        }
    }
</style>
