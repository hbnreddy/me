<template>
  <div class="plan-actions">
    <payment-action v-if="currentStep === 'ready-for-payment'" />

    <booking-action v-else-if="currentStep === 'ready-for-booking'" />

    <checkout-action v-else />

    <delete-plan-action />
  </div>
</template>

<script>
import CheckoutAction from './actions/CheckoutAction'
import PaymentAction from './actions/PaymentAction'
import BookingAction from './actions/BookingAction'
import DeletePlanAction from './actions/DeletePlanAction'

import { mapGetters } from 'vuex'

export default {
  components: {
    CheckoutAction,
    PaymentAction,
    BookingAction,
    DeletePlanAction
  },

  data () {
    return {
      paymentModal: false
    }
  },

  computed: {
    ...mapGetters([
      'currentStep'
    ])
  },

  methods: {
    loadingAllSteps (state) {
      const steps = ['travellers', 'travel', 'hotels', 'activities']
      const timeout = !state ? 500 : 0

      setTimeout(() => {
        for (const step of steps) {
          this.$eventHub.emit(`${step}.loading`, state)
        }
      }, timeout)
    }
  }
}
</script>
