import Vue from 'vue'
import VueRouter from 'vue-router'
import Login from '../components/auth/Login.vue'
import NotFound from '../components/pages/errors/404.vue'
import Dashboard from '../components/pages/Dashboard.vue'

// purchasing
import PurchaseRequest from '../components/pages/purchasing/PurchaseRequest.vue'
import PurchaseApproval from '../components/pages/purchasing/PurchaseApproval.vue'
import PurchaseOrder from '../components/pages/purchasing/PurchaseOrder.vue'
import PurchaseRelease from '../components/pages/purchasing/PurchaseRelease.vue'
import PurchaseDescription from '../components/pages/purchasing/PurchaseDescription.vue'
import PurchaseCloseOrder from '../components/pages/purchasing/PurchaseCloseOrder.vue'
import PurchaseCloseRequest from '../components/pages/purchasing/PurchaseCloseRequest.vue'
// end purchasing

// hr
import Employee from '../components/pages/hr/Employee.vue'
// end hr

// user
import Users from '../components/pages/users/IndexUser.vue'
import AddUser from '../components/pages/users/AddUser.vue'
import EditUser from '../components/pages/users/EditUser.vue'
// end user

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
            guest: true,
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
    // purchase
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
    // human
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
    },
    // users
    {
        path: '/users',
        name: 'users',
        component: Users,
        meta: {
            auth: true,
            title: 'EBS | Data User',
            page: 'Data User',
            description: '',
            tag: 'user',
            submenu: true
        }
    },
    {
        path: '/user/add',
        name: 'addUser',
        component: AddUser,
        meta: {
            auth: true,
            title: 'EBS | Add User',
            page: 'Add User',
            description: '',
            tag: 'user',
            submenu: true
        }
    },
    {
        path: '/user/edit',
        name: 'editUser',
        component: EditUser,
        meta: {
            auth: true,
            title: 'EBS | Edit User',
            page: 'Edit User',
            description: '',
            tag: 'user',
            submenu: true
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
    }else if (to.matched.some(record => record.meta.guest) && loggedIn) {
        next('/dashboard')
        return
    }else if(!to.matched.length){
        next('/notFound');
        return
    }else{
        next()
    }
})

export default router

