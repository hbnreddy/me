<template>
  <div class="plan-overview">
    <div class="position-fixed plan-overview-wrapper">
      <trip-plans-tabs
        :plans="plans"
        @rename="renamePlan($event)"
      />

      <div
        id="myPlansContent"
        class="tab-content"
      >
        <plan-estimate
          v-for="(plan, planIndex) in plans"
          :id="`plan-${plan.id}`"
          :key="planIndex"
          :plan="plan"
        />

        <loading-circle
          :loading="loading"
        />
      </div>
    </div>
  </div>
</template>

<script>
import TripPlansTabs from './TripPlansTabs'
import PlanEstimate from './PlanEstimate'

import {mapGetters} from 'vuex'
import axios from 'axios'
import LoadingCircle from '../../../../../components/LoadingCircle'

export default {
  components: {
    LoadingCircle,
    PlanEstimate,
    TripPlansTabs
  },

  data () {
    return {
      loading: false,
      plans: []
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'checkoutStep',
      'currentPlanId'
    ])
  },

  created () {
    this.fetchPlans()
  },

  methods: {
    async fetchPlans () {
      this.loading = true

      await axios
        .get(`/api/cart/plans`)
        .then(r => {
          if (r.data.success) {
            this.plans = r.data.entities
          }
        })

      this.loading = false
    },

    renamePlan ({ index, name }) {
      this.plans.splice(index, 1, {
        ...this.plans[index],
        name
      })
    }
  }
}
</script>
