<template>
  <div
    class="v-modal"
    tabindex="-1"
    @keydown.esc="hide()"
  >
    <div
      class="modal modal-planner"
    >
      <div
        class="modal-dialog modal-lg"
        role="document"
      >
        <div
          v-on-clickaway="hide"
          class="modal-content"
        >
          <div
            class="modal-body"
            style="display: block"
          >
            <div class="d-flex justify-content-between align-items-start">
              <h4 class="title">
                {{ item.name }}
              </h4>

              <button
                type="button"
                class="close d-block"
                data-dismiss="modal"
                aria-label="Close"
                @click="hide"
              >
                <span style="line-height: .7">&times;</span>
              </button>
            </div>

            <days-row
              :days="calendarDays"
              :active="activeDay"
              :available-flag="false"
              @change="onDayChange($event)"
            />

            <div class="content-wrapper">
              <day-plan
                :items="items[currentDate]"
              />

              <day-scheduler
                :travellers="query.travellers"
                :schedule="schedule"
                :modify="modify"
                @add="addToPlan()"
                @modify="modifyItem()"
                @input="onInputSchedule($event)"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5;" />

    <alert-popup
      v-if="showAlert"
      @hide="showAlert = false"
    >
      <div slot="alert">
        {{ message }}
      </div>
    </alert-popup>
  </div>
</template>

<script>
import * as moment from 'moment'
import {mixin as clickaway} from 'vue-clickaway'
import {mapGetters} from 'vuex'
import DaysRow from '../pages/place/DaysRow'
import DayPlan from '../pages/place/DayPlan'
import DayScheduler from '../pages/place/DayScheduler'
import axios from 'axios'
import AlertPopup from './AlertPopup'

export default {
  components: {
    AlertPopup,
    DayScheduler,
    DayPlan,
    DaysRow
  },

  mixins: [
    clickaway
  ],

  props: {
    item: {
      type: Object,
      required: true
    },

    modify: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      showAlert: false,
      message: '',
      activeDay: 0,
      schedule: false,
      items: {
        //
      },
      product: {
        attributes: {
          title: '',
          type: 'STANDARD',
          place_id: 0,
          marker_id: 0,
          venue_id: 0,
          price: 0,
          total_price: 0
        },
        timeslot: {
          date: false,
          start_time: '06:00',
          end_time: '10:00'
        },
        travellers: []
      },
      calendarDays: []
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'cart',
      'currentPlanId'
    ]),

    currentDate () {
      let a = new moment(this.query.date.start_date)

      a.add(this.activeDay, 'days')

      return a.format(this.$const('BASE_DATE_FORMAT'))
    }
  },

  created () {
    this.resetQuery()
    this.processDays()

    this.initProduct()
  },

  methods: {
    async modifyItem () {
      if (!this.validate()) {
        return false
      }

      if (this.currentPlanId) {
        const success = await this.$store.dispatch('storeItemToPlan', {
          id: this.item.id,
          plan_id: this.currentPlanId,
          item: this.product
        })

        if (success) {
          this.$emit('updated')
        }
      }
    },

    validate () {
      if (!this.schedule.travellersCount) {
        this.message = 'No travellers selected.'
        this.showAlert = true

        return false
      }

      if (!this.checkValid()) {
        this.message = 'Invalid timeslot.'
        this.showAlert = true

        return false
      }

      if (!this.checkAvailable()) {
        this.message = 'This timeslot is already picked. Please choose another timeslot.'
        this.showAlert = true

        return false
      }

      return true
    },

    async fetchItems (date) {
      if (!this.currentPlanId) {
        return false
      }

      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/items`, {
          params: {
            date
          }
        })
        .then(r => {
          this.items = {
            ...this.items,
            [date]: r.data.entities
          }
        })
    },

    initProduct () {
      this.product = {
        ...this.product,
        attributes: {
          title: this.item.name,
          venue_id: this.item.venue_id,
          marker_id: this.item.marker_id,
          place_id: this.item.id,
          city_id: this.item.city_id
        },
        timeslot: {
          ...this.product.timeslot,
          date: this.currentDate
        }
      }
    },

    hide () {
      if (this.showAlert) {
        return false
      }

      this.$emit('hide')
    },

    onInputSchedule (value) {
      this.schedule = {
        ...value
      }

      this.product.timeslot = {
        ...this.product.timeslot,
        start_time: value.startTime,
        end_time: value.endTime
      }

      this.product.travellers = {
        ...value.travellers
      }
    },

    async processDays () {
      const date = this.query.date

      let a = moment(date.start_date)
      const b = moment(date.end_date)

      let diff = b.diff(a, 'days') + 1

      while (diff) {
        this.calendarDays.push({
          day: a.date(),
          month: a.format('MMMM'),
          remainingHours: await this.dayAvailability(a.format(this.$const('BASE_DATE_FORMAT')))
        })

        a.add(1, 'days')

        diff--
      }
    },

    async addToPlan () {
      if (!this.validate()) {
        return false
      }

      if (!this.currentPlanId) {
        await axios
          .post(`/api/cart/plans/store`, {
            origin_city_id: this.query.origin_city.id,
            start_date: this.query.date.start_date,
            end_date: this.query.date.end_date,
            travellers: Object.values(this.query.travellers).join('-')
          })
          .then(async (r) => {
            if (r.data.success) {
              this.$store.commit('setCurrentPlanId', r.data.entity)
            } else {
              alert('An error occured.')
            }
          })
      }

      if (this.currentPlanId) {
        await this.$store.dispatch('storeItemToPlan', {
          plan_id: this.currentPlanId,
          item: this.product
        })

        this.$emit('added')
      }
    },

    checkValid () {
      const start = this.product.timeslot.start_time
      const end = this.product.timeslot.end_time

      if (start >= end) {
        return false
      }

      return true
    },

    checkAvailable () {
      const items = this.items[this.currentDate]
      if (!items || !items.length) {
        return true
      }

      let segments = []
      items.forEach(item => {
        segments.push({
          id: item.id,
          start: item.timeslot.start_time,
          end: item.timeslot.end_time
        })
      })

      segments.push({
        id: this.item.id,
        start: this.schedule.startTime,
        end: this.schedule.endTime
      })

      return this.overlap(segments)
    },

    resetQuery () {
      if (this.modify) {
        this.schedule = {
          travellers: this.item.travellers,
          startTime: this.item.timeslot.start_time,
          endTime: this.item.timeslot.end_time
        }
      } else {
        this.schedule = {
          startTime: '06:00',
          endTime: '10:00'
        }
      }

      this.activeDay = 0

      this.travellers = {
        adults: 0,
        children: 0,
        infants: 0
      }
    },

    overlap (segments) {
      segments.sort((a, b) => {
        if (a.start < b.start) {
          return -1
        } else if (a.start > b.start) {
          return 1
        }

        return 0
      })

      let i = 1
      while (i < segments.length) {
        if ((segments[i - 1].id !== segments[i].id) && segments[i - 1].end >= segments[i].start) {
          return false
        }

        i++
      }

      return true
    },

    async dayAvailability (date) {
      if (!this.items[date]) {
        await this.fetchItems(date)
      }

      if (!this.items[date] || !this.items[date].length) {
        return 18
      }

      let time = 0

      this.items[date].forEach(item => {
        const a = moment(item.timeslot.start_time, 'HH:mm')
        const b = moment(item.timeslot.end_time, 'HH:mm')

        time += b.diff(a, 'minutes')
      })

      return 18 - time / 60
    },

    onDayChange (day) {
      if (this.activeDay === day) {
        return false
      }

      this.activeDay = day

      this.product.timeslot = {
        ...this.product.timeslot,
        date: this.currentDate
      }
    }
  }
}
</script>
