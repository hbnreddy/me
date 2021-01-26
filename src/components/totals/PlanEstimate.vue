<template>
  <div
    :id="id"
    :aria-labelledby="`plan-${plan.id}-tab`"
    class="tab-pane fade show active"
    role="tabpanel"
  >
    <div class="box box-border">
      <estimate-nav :tab="activeTab" @switch="activeTab = $event" />

      <div
        class="plan-content tab-content"
      >
        <totals-summary
          v-if="activeTab === 'all'"
          :totals="totals"
          :currency="currentCurrency"
        />

        <individual-estimate
          v-if="activeTab === 'individual'"
          :travellers="travellersTotals"
          :currency="currentCurrency"
        />

        <coupon
          v-model="coupon"
          :discount="discount"
          :currency="currentCurrency"
        />

        <div class="total-estimate">
          <div>{{ __('Total estimate') }}</div>

          <div style="font-size: 20px;">
            {{ plan.currency }} {{ totals ? totals.total_amount : 0 }}
          </div>
        </div>

        <plan-actions v-if="!isBooking" />
      </div>
    </div>
  </div>
</template>

<script>
import EstimateNav from './EstimateNav'
import TotalsSummary from './TotalsSummary'
import IndividualEstimate from './IndividualEstimate'
import Coupon from './Coupon'
import PlanActions from './PlanActions'

import { mapGetters } from 'vuex'
import {validate} from '../../mixins/validate'

export default {
  components: {
    PlanActions,
    Coupon,
    EstimateNav,
    TotalsSummary,
    IndividualEstimate
  },

  mixins: [
    validate
  ],

  props: {
    id: {
      type: String,
      required: true
    },

    plan:  {
      type: Object,
      required: true
    },

    totals:  {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      activeTab: 'all',
      loading: false,
      coupon: {
        code: '',
        added: false
      },
      discount: {
        amount: 450
      }
    }
  },

  computed: {
    ...mapGetters([
      'currentCurrency'
    ]),

    travellersTotals () {
      return this.plan.travellers.map(e => {
        return {
          ...e,
          total_amount: this.totals.travellers[e.id]
        }
      })
    }
  }
}
</script>
