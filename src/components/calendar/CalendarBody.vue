<template>
  <div class="vuecal__body calendar-body">
    <div class="body-wrap">
      <div class="body-bg">
        <div class="vuecal__flex">
          <div class="vuecal__time-column">
            <hour-lines :hours="hours" />
          </div>

          <div
            class="flex-fill chunk selected-chunk"
          >
            <div
              v-for="(day, index) in daysChunk"
              :key="index"
              :class="{'selected-date' : moment(day.date).format('YYYY MMM DD') === moment().format('YYYY MMM DD') }"
              class="vuecal__cell"
            >
              <calendar-column
                :day="day"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import HourLines from './HourLines'
import CalendarColumn from './CalendarColumn'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import 'vue-cal/dist/vuecal.css'
import axios from 'axios'

export default {
  components: {
    HourLines,
    CalendarColumn
  },

  mixins: [
    clickaway
  ],

  props: {
    daysChunk: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      selectedChunk: 0,
      headings: []
    }
  },

  computed: {
    ...mapGetters([
      'plans'
    ]),

    hours () {
      let hours = []

      for (let i = 6; i < 24; i++) {
        if (i < 10) {
          hours.push(`0${i}:00`)
          hours.push(`0${i}:30`)
        } else {
          hours.push(`${i}:00`)
          hours.push(`${i}:30`)
        }
      }

      hours.push(`00:00`)
      hours.push(`00:30`)

      for (let i = 1; i < 6; i++) {
        hours.push(`0${i}:00`)
        hours.push(`0${i}:30`)
      }

      return hours
    },

    currentPlanObject () {
      return this.plans.find(e => {
        return e.id === this.currentPlan
      })
    }
  },

  watch: {
    daysChunk: {
      handler (value) {
        //
      }
    }
  },

  methods: {
    async processItemsPerDate () {
      for (const e of this.headings) {
        this.items = {
          ...this.items,
          [e.date]: await this.processItems(e.date)
        }
      }
    },

    async processItems (date) {
      if (!this.currentPlan) {
        return []
      }

      let items = []

      await axios
        .get(`/cart/plans/${this.currentPlanId}/items`, {
          params: {
            date
          }
        })
        .then(r => {
          items = r.data.entities.map(e => {
            return {
              ...e,
              travellers: this.processTravellers(e.travellers)
            }
          })
        })

      return items
    },

    processTravellers (travellers) {
      let results = []

      const planTravellers = this.currentPlanObject.travellers

      let index = 0
      while (index < travellers.adults.length) {
        if (travellers.adults[index].attributes.active) {
          if (planTravellers.adults[index].attributes.key) {
            results.push(planTravellers.adults[index].attributes.key)
          } else {
            results.push(`A${index + 1}`)
          }
        }

        index++
      }

      index = 0
      while (index < travellers.children.length) {
        if (travellers.children[index].attributes.active) {
          if (planTravellers.children[index].attributes.key) {
            results.push(planTravellers.children[index].attributes.key)
          } else {
            results.push(`C${index + 1}`)
          }
        }

        index++
      }

      index = 0
      while (index < travellers.infants.length) {
        if (travellers.infants[index].attributes.active) {
          if (planTravellers.infants[index].attributes.key) {
            results.push(planTravellers.infants[index].attributes.key)
          } else {
            results.push(`I${index + 1}`)
          }
        }

        index++
      }

      return results
    },

    /**
     * TODO: Check this.
     */
    showCurrentChunk () {
      document.querySelectorAll('.chunk').forEach(e => {
        e.style.display = 'none'
      })
      document.querySelector(`.chunk.chunk-${this.selectedChunk}`).style.display = 'flex'
    }
  }
}
</script>
