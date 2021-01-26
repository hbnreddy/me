<template>
  <div class="d-flex flex-column flex-fill calendar">
    <calendar-header
      :days-chunks="daysChunks"
      :selected-chunk="selectedChunk"
      @selected="onSelectChunk($event)"
    />

    <calendar-body
      :days-chunk="currentDaysChunk"
    />
  </div>
</template>

<script>
import CalendarHeader from './CalendarHeader'
import CalendarBody from './CalendarBody'

import * as moment from 'moment'
import {chunk} from 'lodash'
import { mapGetters } from 'vuex'

export default {
  components: {
    CalendarBody,
    CalendarHeader
  },

  data () {
    return {
      selectedChunk: 0
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    currentDaysChunk () {
      return this.daysChunks[this.selectedChunk]
    },

    daysChunks () {
      let days = []

      let a = moment(this.currentPlan.start_date)
      const b = moment(this.currentPlan.end_date)

      let diff = b.diff(a, 'days') + 1

      while (diff) {
        days.push({
          city: false,
          date: a.format(this.$const('BASE_DATE_FORMAT'))
        })

        a.add(1, 'days')

        diff--
      }

      if (!days.length) {
        return []
      }

      return chunk(days, 3)
    }
  },

  methods: {
    onSelectChunk (value) {
      this.selectedChunk = value
    }
  }
}
</script>
