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
import {mapGetters} from 'vuex'
import {chunk} from 'lodash'

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
      'query'
    ]),

    currentDaysChunk () {
      return this.daysChunks[this.selectedChunk]
    },

    daysChunks () {
      let days = []
      const date = this.query.date

      let a = moment(date.start_date)
      const b = moment(date.end_date)

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
