<template>
  <div class="checkout-page">
    <booking-steps />

    <trip-estimate
      :plan="currentPlan"
      :totals="totals"
      @renamePlan="renamePlan"
    />
  </div>
</template>

<script>
import TripEstimate from '../../components/totals/PlanTotals'
import BookingSteps from './components/BookingSteps'

import {mapGetters} from 'vuex'

export default {
  components: {
    BookingSteps,
    TripEstimate
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    totals () {
      return {
        activity_amount: 0,
        currency: 'USD',
        fees: 0,
        hotel_amount: 0,
        standard_amount: 0,
        total_amount: 0,
        travel_amount: 0,
        travellers: []
      }
    }
  },

  methods: {
    renamePlan (planId, name) {
      //
    }
  }
}
</script>
