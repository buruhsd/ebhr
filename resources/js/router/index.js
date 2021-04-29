import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../components/auth/Login.vue'
import Dashboard from '../components/pages/Dashboard.vue'
import PurchaseRequest from '../components/pages/purchasing/PurchaseRequest.vue'
import PurchaseApproval from '../components/pages/purchasing/PurchaseApproval.vue'
import PurchaseOrder from '../components/pages/purchasing/PurchaseOrder.vue'
import PurchaseRelease from '../components/pages/purchasing/PurchaseRelease.vue'
import PurchaseDescription from '../components/pages/purchasing/PurchaseDescription.vue'
import PurchaseCloseOrder from '../components/pages/purchasing/PurchaseCloseOrder.vue'
import PurchaseCloseRequest from '../components/pages/purchasing/PurchaseCloseRequest.vue'
import Employee from '../components/pages/hr/Employee.vue'
import NotFound from '../components/pages/errors/404.vue'

Vue.use(VueRouter)

const routes = [
    {
        path: "*",
        name: "NotFound",
        component: NotFound,
        meta: {
            auth: false,
            title: 'EBS | NotFound'
        }
    },
    {
        path: '/',
        redirect: {
            name: "login"
        }
    },
    {
        path: "/login",
        name: "login",
        component: Login,
        meta: {
            auth: false,
            title: 'EBS | Login'
        }
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            auth: true,
            title: 'EBS | Dashboard',
            page: 'Dashboard',
            description: '',
        }
    },
    {
        path: '/purchase/request',
        name: 'purchaseRequest',
        component: PurchaseRequest,
        meta: {
            auth: true,
            title: 'EBS | Permintaan Pembelian',
            page: 'Permintaan Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/purchase/approval',
        name: 'purchaseApproval',
        component: PurchaseApproval,
        meta: {
            auth: true,
            title: 'EBS | Approval Pembelian',
            page: 'Approval Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/purchase/order',
        name: 'purchaseOrder',
        component: PurchaseOrder,
        meta: {
            auth: true,
            title: 'EBS | Order Pembelian',
            page: 'Order Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/purchase/release/order',
        name: 'purchaseRelease',
        component: PurchaseRelease,
        meta: {
            auth: true,
            title: 'EBS | Release Order Pembelian',
            page: 'Release Order Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/purchase/description',
        name: 'purchaseDescription',
        component: PurchaseDescription,
        meta: {
            auth: true,
            title: 'EBS | Uraian Pembelian',
            page: 'Uraian Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/purchase/close/order',
        name: 'purchaseCloseOrder',
        component: PurchaseCloseOrder,
        meta: {
            auth: true,
            title: 'EBS | Penutupan Order Pembelian',
            page: 'Penutupan Order Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/purchase/close/request',
        name: 'purchaseCloseRequest',
        component: PurchaseCloseRequest,
        meta: {
            auth: true,
            title: 'EBS | Penutupan Permintaan Pembelian',
            page: 'Penutupan Permintaan Pembelian',
            description: '',
            tag: 'purchase',
        }
    },
    {
        path: '/human/employee',
        name: 'employee',
        component: Employee,
        meta: {
            auth: true,
            title: 'EBS | Data Karyawan',
            page: 'Data Karyawan',
            description: '',
            tag: 'human',
        }
    }
];

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

router.beforeEach((to, from, next) => {
    const loggedIn = localStorage.getItem('user')
    if (to.matched.some(record => record.meta.auth) && !loggedIn) {
        next('/login')
        return
    }
    next()
})

router.beforeEach((to, from, next) => {
    if (!to.matched.length) {
      next('/notFound');
    } else {
      next();
    }
});

export default router

