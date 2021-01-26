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
                {{ item.title }}
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
              :available-days="availableDays"
              :active="activeDay"
              @change="onDayChange($event)"
            />

            <div class="content-wrapper">
              <day-plan
                :items="items[currentDate]"
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
import DaysRow from '../pages/place/DaysRow'
import DayPlan from '../pages/place/DayPlan'
import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import axios from 'axios'
import AlertPopup from './AlertPopup'

export default {
  components: {
    AlertPopup,
    DaysRow,
    DayPlan
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
      cartItems: [],
      cartItem: {
        travellers: {
          adults: [],
          children: [],
          infants: []
        }
      },
      availableTimeslots: [],
      availableDays: [],
      schedule: false,
      travellers: {
        //
      },
      product: {
        attributes: {
          title: '',
          type: 'activity',
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
      items: {
        //
      },
      message: '',
      showAlert: false,
      totalPrice: 0,
      price: {
        adults: false,
        children: false,
        infants: false
      },
      calendarDays: [],
      activities: [],
      activeDay: 0,
      travellersPicker: false,
      activitiesTravellersPicker: false
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'currentPlanId',
      'cart'
    ]),

    computedTravellers () {
      const items = this.query.travellers

      let travellers = []
      Object.keys(items).forEach(key => {
        for (let i = 0; i < items[key]; ++i) {
          travellers.push({
            key: `${key[0].toUpperCase()}${i + 1}`,
            type: key
          })
        }
      })

      return travellers
    },

    isCurrentDayAvailable () {
      return this.availableDays.includes(this.currentDate)
    },

    currentDate () {
      let a = new moment(this.query.date.start_date)

      a.add(this.activeDay, 'days')

      return a.format(this.$const('BASE_DATE_FORMAT'))
    }
  },

  created () {
    this.resetQuery()
    this.processDays()
    this.processPrice()

    if (this.item.type === 'standard') {
      this.availableDays = this.calendarDays.map(e => {
        return e.date
      })
    }

    this.getAvailableDates()
  },

  methods: {
    onChange (items) {
      this.cartItems = items
    },

    async getAvailableDates () {
      await axios
        .get(`/api/activities/${this.item.uuid}/dates`, {
          params: {
            date: this.currentDate,
            start_date: this.query.date.start_date,
            end_date: this.query.date.end_date
          }
        })
        .then(r => {
          this.availableDays = r.data.entities

          if (this.isCurrentDayAvailable) {
            this.getCurrentDayInfo()
          }
        })
    },

    async getCurrentDayInfo () {
      await axios
        .get(`/api/activities/${this.item.uuid}/date-info`, {
          params: {
            date: this.currentDate
          }
        })
        .then(r => {
          if (r.data.success) {
            this.processDayInfo(r.data.entity)
          }
        })
    },

    processDayInfo (info) {
      this.availableTimeslots = [
        ...info.timeslots.map(e => {
          return {
            ...e,
            start_time: e.time,
            end_time: false
          }
        })
      ]
    },

    async modifyItem () {
      if (!this.validate()) {
        return false
      }

      if (this.currentPlanId) {
        const success = await this.$store.dispatch(storeItemToPlan, {
          id: this.item.id,
          plan_id: this.currentPlanId,
          item: this.product
        })

        if (success) {
          this.$emit('updated')
        }
      }
    },

    onDayChange (day) {
      if (this.activeDay === day) {
        return false
      }

      this.availableTimeslots = []

      this.activeDay = day

      this.product.timeslot = {
        ...this.product.timeslot,
        date: this.currentDate
      }

      if (this.isCurrentDayAvailable) {
        this.getCurrentDayInfo()
      }
    },

    hide () {
      if (this.showAlert) {
        return false
      }

      this.$emit('hide')
    },

    onInputTravellers (value) {
      this.travellers = {
        ...value
      }

      this.product.travellers = {
        ...value
      }
    },

    onInputSchedule (value) {
      const duration = this.item.duration
        ? this.item.duration
        : this.item.timeslot.duration
      this.schedule = {
        ...value,
        end: this.timeAfterMinutes(value.start, duration)
      }

      this.product.timeslot = {
        ...this.product.timeslot,
        ...value,
        start_time: value.start,
        end_time: this.schedule.end
      }

      this.product.attributes = {
        ...this.product.attributes,
        price: value.adults ? value.adults.price : 0,
        price_children: value.children ? value.children.price : 0,
        price_infants: value.infants ? value.infants.price : 0
      }
    },

    validate () {
      if (!this.isCurrentDayAvailable) {
        this.message = 'This day is not available.'
        this.showAlert = true

        return false
      }

      let travellersCount = this.travellers.adults
        + this.travellers.children
        + this.travellers.infants
      if (this.modify) {
        travellersCount = this.item.travellers.length
      }

      if (!travellersCount) {
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

    async addToPlan () {
      // if (!this.validate()) {
      //   return false
      // }

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
        const success = await this.$store.dispatch('addItemsToCart', {
          activity_id: this.item.uuid,
          place_id: this.item.place_id,
          marker_id: this.item.marker_id,
          city_id: this.item.city_id,
          type: 'activity',
          items: this.cartItems
        })

        if (success) {
          this.$emit('added')
        }
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
      const startTime = this.schedule.start
      const endTime = this.schedule.end

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
        start: startTime,
        end: endTime
      })

      return this.overlap(segments)
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

    processPrice () {
      const value = this.modify
        ? this.item.price
        : this.item.retail_price.value

      this.price = {
        adults: value,
        children: value,
        infants: value
      }
    },

    resetQuery () {
      this.activeDay = 0

      if (this.modify) {
        this.schedule = {
          timeslots: [],
          travellers: this.item.travellers,
          start: this.item.timeslot.start_time,
          end: this.timeAfterMinutes(this.item.timeslot.start_time, this.item.timeslot.duration),
          duration: this.item.timeslot.duration
        }
      } else {
        this.schedule = {
          timeslots: [],
          start: false,
          end: false
        }

        this.travellers = {
          adults: 0,
          children: 0,
          infants: 0
        }
      }
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

    timeAfterMinutes (time, minutes) {
      const temp = parseInt(time.split(':')[0])

      const hours = parseInt(minutes / 60)

      let timeHour = temp + hours

      if (timeHour > 23) {
        timeHour = timeHour % 24
      }

      if (timeHour < 10) {
        timeHour = `0${timeHour}`
      }

      let mins = minutes - (60 * hours)

      let timeMinutes = '00'

      if (mins) {
        timeMinutes = mins

        if (mins < 10) {
          timeMinutes = `0${mins}`
        }
      }

      return `${timeHour}:${timeMinutes}`
    },

    async dayAvailability (date) {
      if (!this.currentPlanId) {
        return 18
      }

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

    async processDays () {
      const date = this.query.date

      let a = moment(date.start_date)
      const b = moment(date.end_date)

      let diff = b.diff(a, 'days') + 1

      while (diff) {
        this.calendarDays.push({
          date: a.format(this.$const('BASE_DATE_FORMAT')),
          day: a.date(),
          month: a.format('MMMM'),
          remainingHours: await this.dayAvailability(a.format(this.$const('BASE_DATE_FORMAT')))
        })

        a.add(1, 'days')

        diff--
      }
    },

    selectActiveDay (index) {
      this.activeDay = index
    },

    showTravellersPicker () {
      this.travellersPicker = true
    },

    showActivitiesTravellersPicker () {
      this.activitiesTravellersPicker = true
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    hideActivitiesTravellersPicker () {
      this.activitiesTravellersPicker = false
    }
  }
}
</script>
