<template>
    <div id="main">
        <div v-if="$route.meta.auth">
            <menuMobile></menuMobile>
            <div class="d-flex flex-column flex-root">
                <div class="d-flex flex-row flex-column-fluid page">
                    <menuSidebar></menuSidebar>
                    <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
                        <menuHeader></menuHeader>
                        <div class="content d-flex flex-column flex-column-fluid" id="kt_content" style="min-height: 100vh;">
                            <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
                                <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
                                    <div class="align-items-center mr-2">
                                        <h5 class="text-dark font-weight-bold my-2 mr-5">{{capitalizeFirstLetter($route.meta.page)}} <small>{{$route.meta.description}}</small></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column-fluid">
                                <div class="container">
                                    <div id="content">
                                        <router-view></router-view>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <menuFooter></menuFooter>
                    </div>
                </div>
            </div>
            <menuQuick></menuQuick>
            <menuScorlltop></menuScorlltop>
        </div>
        <div v-if="!$route.meta.auth" class="d-flex flex-column flex-root">
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
    import menuMobile from './menu/MenuMobile.vue'
    import menuHeader from './menu/MenuHeader.vue'
    import menuQuick from './menu/MenuQuick.vue'
    import menuSidebar from './menu/MenuSidebar.vue'
    import menuFooter from './menu/MenuFooter.vue'
    import menuScorlltop from './menu/MenuScrolltop.vue'
    export default {
        data() {
            return {
                isLogin: false
            }
        },
        methods:{
            capitalizeFirstLetter(str) {
               return str[0].toUpperCase() + str.slice(1);
            }
        },
        components: {
            menuMobile,
            menuHeader,
            menuSidebar,
            menuFooter,
            menuQuick,
            menuScorlltop
        }
    }
</script>
