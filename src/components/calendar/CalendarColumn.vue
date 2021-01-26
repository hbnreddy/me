<template>
  <div
    column=""
    tabindex="0"
    class="vuecal__flex vuecal__cell-content calendar-column"
  >
    <div
      v-if="loading || !items.length"
      class="vuecal__no-event"
    >
      <no-plans-exception
        :loading="loading"
      />
    </div>

    <div
      v-else
      class="vuecal__cell-events"
    >
      <day-column
        :items="items"
      />
    </div>
  </div>
</template>

<script>
import NoPlansException from './NoPlansException'
import DayColumn from './DayColumn'
import moment from 'moment'

import {mapGetters} from 'vuex'

export default {
  components: {
    NoPlansException,
    DayColumn
  },

  props: {
    day: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      loading: false
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    items () {
      return this.currentPlan.items.filter(e => {
        return moment(e.timeslot.start_date).format('YYYY-MM-DD') === this.day.date
      })
    }
  }
}
</script>
