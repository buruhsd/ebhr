<template>
    <div :class=" aside ? 'aside aside-left aside-fixed d-flex flex-column flex-row-auto aside-on' : 'aside aside-left aside-fixed d-flex flex-column flex-row-auto'" id="kt_aside">
        <div class="brand flex-column-auto" id="kt_brand">
            <router-link :to="{ name: 'dashboard' }" class="brand-logo">
                <!-- <img width="30px" class="mr-3" alt="Logo" v-bind:src="'/media/logos/logo-4.png'" /> -->
                <span class="font-size-h2 text-white"><b>EBSystem</b></span>
            </router-link>
        </div>
        <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
            <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
                <ul class="menu-nav">
                    <li class="menu-section">
                        <h4 class="menu-text">Main</h4>
                        <i class="menu-icon ki ki-bold-more-hor icon-md"></i>
                    </li>
                    <li :class="$route.name === 'dashboard' ? 'menu-item menu-item-active' : 'menu-item'" aria-haspopup="true">
                        <router-link :to="{ name: 'dashboard' }" class="menu-link">
                            <span class="svg-icon menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <polygon points="0 0 24 0 24 24 0 24" />
                                        <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
                                        <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Dashboard</span>
                        </router-link>
                    </li>
                    <li :class="$route.meta.tag === 'human' || openMenuHuman ? 'menu-item menu-item-submenu menu-item-open menu-item-here' : 'menu-item menu-item-submenu'" aria-haspopup="true" data-menu-toggle="hover">
                        <a href="#" target="_self" class="menu-link menu-toggle" @click="openMenu('human')">
                            <span class="svg-icon menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"></rect>
                                        <rect fill="#000000" opacity="0.3" x="4" y="4" width="8" height="16"></rect>
                                        <path d="M6,18 L9,18 C9.66666667,18.1143819 10,18.4477153 10,19 C10,19.5522847 9.66666667,19.8856181 9,20 L4,20 L4,15 C4,14.3333333 4.33333333,14 5,14 C5.66666667,14 6,14.3333333 6,15 L6,18 Z M18,18 L18,15 C18.1143819,14.3333333 18.4477153,14 19,14 C19.5522847,14 19.8856181,14.3333333 20,15 L20,20 L15,20 C14.3333333,20 14,19.6666667 14,19 C14,18.3333333 14.3333333,18 15,18 L18,18 Z M18,6 L15,6 C14.3333333,5.88561808 14,5.55228475 14,5 C14,4.44771525 14.3333333,4.11438192 15,4 L20,4 L20,9 C20,9.66666667 19.6666667,10 19,10 C18.3333333,10 18,9.66666667 18,9 L18,6 Z M6,6 L6,9 C5.88561808,9.66666667 5.55228475,10 5,10 C4.44771525,10 4.11438192,9.66666667 4,9 L4,4 L9,4 C9.66666667,4 10,4.33333333 10,5 C10,5.66666667 9.66666667,6 9,6 L6,6 Z" fill="#000000" fill-rule="nonzero"></path>
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Human Resources</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="menu-submenu">
                            <i class="menu-arrow"></i>
                            <ul class="menu-subnav">
                                <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span class="menu-text">Human Resources</span></span></li>
                                <li :class="$route.name === 'employee' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'employee' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Data Karyawan</span></router-link></li>
                            </ul>
                        </div>
                    </li>
                    <li :class="$route.meta.tag === 'purchase' || openMenuPurchase ? 'menu-item menu-item-submenu menu-item-open menu-item-here' : 'menu-item menu-item-submenu'" aria-haspopup="true" data-menu-toggle="hover">
                        <a href="#" target="_self" class="menu-link menu-toggle" @click="openMenu('purchase')">
                            <span class="svg-icon menu-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24"/>
                                        <path d="M4,4 L20,4 C21.1045695,4 22,4.8954305 22,6 L22,18 C22,19.1045695 21.1045695,20 20,20 L4,20 C2.8954305,20 2,19.1045695 2,18 L2,6 C2,4.8954305 2.8954305,4 4,4 Z" fill="#000000" opacity="0.3"/>
                                        <path d="M18.5,11 L5.5,11 C4.67157288,11 4,11.6715729 4,12.5 L4,13 L8.58578644,13 C8.85100293,13 9.10535684,13.1053568 9.29289322,13.2928932 L10.2928932,14.2928932 C10.7456461,14.7456461 11.3597108,15 12,15 C12.6402892,15 13.2543539,14.7456461 13.7071068,14.2928932 L14.7071068,13.2928932 C14.8946432,13.1053568 15.1489971,13 15.4142136,13 L20,13 L20,12.5 C20,11.6715729 19.3284271,11 18.5,11 Z" fill="#000000"/>
                                        <path d="M5.5,6 C4.67157288,6 4,6.67157288 4,7.5 L4,8 L20,8 L20,7.5 C20,6.67157288 19.3284271,6 18.5,6 L5.5,6 Z" fill="#000000"/>
                                    </g>
                                </svg>
                            </span>
                            <span class="menu-text">Purchasing</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="menu-submenu">
                            <i class="menu-arrow"></i>
                            <ul class="menu-subnav">
                                <li class="menu-item menu-item-parent" aria-haspopup="true"><span class="menu-link"><span class="menu-text">Purchasing</span></span></li>
                                <li :class="$route.name === 'purchaseRequest' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseRequest' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Permintaan Pembelian</span></router-link></li>
                                <li :class="$route.name === 'purchaseOrder' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseOrder' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Order Pembelian</span></router-link></li>
                                <li :class="$route.name === 'purchaseDescription' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseDescription' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Uraian Pembelian</span></router-link></li>
                                <li :class="$route.name === 'purchaseApproval' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseApproval' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Approval Pembelian</span></router-link></li>
                                <li :class="$route.name === 'purchaseRelease' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseRelease' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Release Order Pembelian</span></router-link></li>
                                <li :class="$route.name === 'purchaseCloseOrder' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseCloseOrder' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Penutupan Order Pembelian</span></router-link></li>
                                <li :class="$route.name === 'purchaseCloseRequest' ? 'menu-item menu-item-submenu menu-item-active' : 'menu-item menu-item-submenu'" aria-haspopup="true">
                                    <router-link :to="{ name: 'purchaseCloseRequest' }" class="menu-link "><i class="menu-bullet menu-bullet-dot"><span></span></i><span class="menu-text">Penutupan Permintaan Pembelian</span></router-link></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['aside'],
        data() {
            return {
                openMenuHuman: false,
                openMenuPurchase: false,
            }
        },
        mounted() {
            console.log("sidebar mounted")
        },
        methods:{
            openMenu(type) {
                if(type == 'human'){
                    this.openMenuHuman = !this.openMenuHuman
                }else if(type == 'purchase'){
                    this.openMenuPurchase = !this.openMenuPurchase
                }
                console.log('human '+this.openMenuHuman);
                console.log('prc '+this.openMenuPurchase);
            }
        },
        created() {
            console.log("sidebar created")
        },
    }
</script>
