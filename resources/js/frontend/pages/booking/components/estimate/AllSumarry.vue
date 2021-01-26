<template>
  <div
    id="nav-summary"
    class="tab-pane fade show active"
    role="tabpanel"
    aria-labelledby="nav-activities-tab"
  >
    <div class="tab-title">
      {{ __('All Expenses') }}
    </div>

    <div>
      <div
        v-for="(plan, planIndex) in planner"
        :key="planIndex"
        class="plan"
      >
        <div class="title">
          <i class="fa fa-money mr-2 icon" />
          {{ plan.title }}
        </div>

        <div class="price">
          {{ query.currency.symbol }} {{ plan.price }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  props: {
    totals: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      planner: {
        flights: {
          title: 'Flights',
          price: 0
        },
        hotels: {
          title: 'Hotels',
          price: 0
        },
        activities: {
          title: 'Activities & Experiences',
          price: 0
        }
      }
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ])
  },

  created () {
    if (this.totals) {
      this.planner.activities.price = this.totals.activities_price
      this.planner.hotels.price = this.totals.hotels_price
      this.planner.flights.price = this.totals.flights_price
    }
  }
}
</script>
