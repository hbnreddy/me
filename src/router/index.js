import Vue from 'vue'
import VueRouter from 'vue-router'
import WelcomePage from '../views/welcome/WelcomePage'
import ExplorePage from '../views/explore/ExplorePage'
import CityPage from '../views/city/CityPage'
import PlacePage from '../views/place/PlacePage'
import CheckoutPage from '../views/checkout/CheckoutPage'
import BookingPage from '../views/booking/BookingPage'

Vue.use(VueRouter)

const routes = [{
        path: '/en',
        name: 'welcome',
        component: WelcomePage
    },
    {
        name: 'explore',
        path: '/en/explore',
        component: ExplorePage
    },
    {
        name: 'city',
        path: '/city/:id/:index/:planId',
        component: CityPage
    },
    {
        name: 'place',
        path: '/place/:cityId/:cityIndex/:placeId/:planId',
        component: PlacePage
    },
    {
        name: 'checkout',
        path: '/checkout/:planId',
        component: CheckoutPage
    },
    {
        name: 'booking',
        path: '/booking/:orderId',
        component: BookingPage
    },
    {
        path: '/about',
        name: 'About',
        // route level code-splitting
        // this generates a separate chunk (about.[hash].js) for this route
        // which is lazy-loaded when the route is visited.
        component: () =>
            import ( /* webpackChunkName: "about" */ '../views/About.vue')
    }
]

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
})

export default router