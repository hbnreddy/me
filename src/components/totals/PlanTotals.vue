<template>
  <div class="plan-overview">
    <div class="position-sticky plan-overview-wrapper">
      <plan-totals-header
        :plan="plan"
        @rename="renamePlan"
      />

      <div
        class="tab-content"
      >
        <plan-estimate
          :id="`plan-${plan.id}`"
          :plan="plan"
          :totals="totals"
        />

        <loading-circle
          :loading="loading"
        />
      </div>
    </div>
  </div>
</template>

<script>
import PlanTotalsHeader from './PlanTotalsHeader'
import PlanEstimate from './PlanEstimate'
import LoadingCircle from '../loading/LoadingCircle'

export default {
  components: {
    LoadingCircle,
    PlanEstimate,
    PlanTotalsHeader
  },

  props: {
    plan: {
      type: Object,
      required: true
    },

    totals: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      loading: false
    }
  },

  methods: {
    renamePlan (planId, name) {
      this.$emit('rename-plan', planId, name)
    }
  }
}
</script>
