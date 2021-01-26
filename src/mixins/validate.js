import { mapGetters } from 'vuex'
import { NOT_LOGGED_IN, TRAVELLER_EMAIL_MISSMATCH } from '../bootstrap/notify-messages'

export const validate = {
    computed: {
        ...mapGetters([
            'user',
            'currentPlan',
            'steps'
        ]),

        checkoutTravellers() {
            return this.steps.travellers.complete
        },

        canCheckout() {
            return this.checkAuth()
        },

        isBooking() {
            return !this.currentPlan
        },

        stepsComplete() {
            return Object.values(this.steps).every(e => e.complete)
        }
    },

    methods: {
        checkAuth() {
            if (!this.user) {
                this.$notification.show(NOT_LOGGED_IN)

                return false
            }

            return true
        },

        isMainCustomer(customer) {
            return this.user.email === customer.email
        },

        checkMainCustomer(customers) {
            for (let i = 0; i < customers.length; ++i) {
                if (this.isMainCustomer(customers[i])) {
                    return true
                }
            }

            this.$notification.show(TRAVELLER_EMAIL_MISSMATCH)

            return false
        }
    }
}