<template>
  <div v-if="1 === 0 && order" class="checkout-page booking-page">
    <div class="plans">
      <div class="tab-content" style="padding: 20px 0;">
        <calendar />
      </div>
    </div>

    <trip-estimate
      v-if="1 === 0"
      :plan="computedPlan"
      :totals="order.totals"
      @renamePlan="renamePlan"
    />
  </div>
</template>

<script>
import TripEstimate from '../../components/totals/PlanTotals'
import Calendar from '../../components/calendar/Calendar'

export default {
  components: {
    Calendar,
    TripEstimate
  },

  data () {
    return {
      order: null
    }
  },

  computed: {
    computedPlan () {
      return {
        ...this.order.plan,
        currency: this.order.currency
      }
    }
  },

  created () {
    this.fetchOrder()
  },

  methods: {
    async fetchOrder () {
      this.order = await this.$store.dispatch('fetchOrder', this.$route.params.orderId)
    },

    renamePlan (planId, name) {
      //
    }
  }
}
</script>
