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

    <div class="summaries">
      <div
        v-for="(summary, planIndex) in summaries"
        :key="planIndex"
        class="plan"
      >
        <div class="title">
          <i class="fa fa-money mr-2 icon" />
          {{ summary.title }}
        </div>

        <div class="price">
          {{ currency }} {{ totals[summary.key] }}
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
    },

    currency: {
      type: String,
      default: () => '$ (default)'
    }
  },

  data () {
    return {
      summaries: [
        {
          title: 'Travel',
          key: 'travel_amount'
        },
        {
          title: 'Stay',
          key: 'hotel_amount'
        },
        {
          title: 'Activities & Experiences',
          key: 'activity_amount'
        }
      ]
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ])
  }
}
</script>
