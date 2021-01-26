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
          >
            <div class="d-flex justify-content-between align-items-start">
              <h4 class="title mb-4">
                {{ item.name }}
              </h4>

              <button
                type="button"
                class="close d-block"
                data-dismiss="modal"
                aria-label="Close"
                @click="hide"
              >
                <span style="line-height: .7;">&times;</span>
              </button>
            </div>

            <days-row
              :days="planDays"
              :loading="daysRowLoading"
              :active="activeDayIndex"
              @change="onChangeDay($event)"
            />

            <div class="content-wrapper">
              <day-plan
                :loading="dayPlanLoading"
                :items="allItems"
                :currency="query.currency"
              />

              <standard-day-availability
                v-if="!isActivity"
                :modify="modify"
                :btn-loading="btnLoading"
                :btn-text="btnText"
                :travellers="tempTravellers"
                :timeslots="availableTimeslots"
                :timeslot="timeslot"
                @timeslot="onInputTimeslot($event)"
                @travellers="onInputTravellers($event)"
                @update="addToPlan()"
              />

              <day-availability
                v-else
                :modify="modify"
                :btn-loading="btnLoading"
                :btn-text="btnText"
                :loading="loadingAvailability"
                :available="isCurrentDayAvailable"
                :travellers="tempTravellers"
                :timeslots="availableTimeslots"
                :currency="query.currency"
                @travellers="onInputTravellers($event)"
                @update="addToPlan()"
                @timeslot="onInputTimeslot($event)"
                @change="onChange($event)"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5;" />
  </div>
</template>

<script>
import StandardDayAvailability from '../pages/place/StandardDayAvailability'
import DaysRow from '../pages/place/DaysRow'
import DayAvailability from '../pages/place/DayAvailability'
import DayPlan from '../pages/place/DayPlan'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import { duration } from '../mixins/duration'
import axios from 'axios'
import { query } from '../mixins/query'

export default {
  components: {
    StandardDayAvailability,
    DayAvailability,
    DaysRow,
    DayPlan
  },

  mixins: [
    clickaway,
    query,
    duration
  ],

  props: {
    id: {
      type: String | Boolean,
      default: false
    },

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
      daysRowLoading: false,
      travellers: [],
      days: [],
      btnText: '',
      rides: [],
      btnLoading: false,
      dayPlanItems: [],
      dayPlanLoading: false,
      tempTravellers: [],
      durationMinutes: false,
      loadingAvailability: false,
      planDays: [],
      cartItems: [],
      availableTimeslots: [],
      timeslot: {
        start_time: false,
        end_time: false
      },
      activeDayIndex: 0
    }
  },

  computed: {
    ...mapGetters([
      'authUser',
      'query',
      'currentPlanId',
      'cart'
    ]),

    allItems () {
      return [
        ...this.dayPlanItems.filter(e => {
          return e.type !== 'ride'
        }),
        ...this.rides
      ]
    },

    isCurrentDayAvailable () {
      return !!(this.currentDay && this.currentDay.available && this.availableTimeslots.length)
    },

    isActivity () {
      return this.item.type === 'activity'
    },

    currentDay () {
      return this.planDays[this.activeDayIndex]
    }
  },

  created () {
    this.travellers = this.queryTravellers
    this.fetchAvailableDays()

    if (this.modify) {
      this.processTravellers()
    } else {
      this.tempTravellers = [
        ...this.travellers
      ]
    }

    this.init()
  },

  methods: {
    async fetchAvailableDays () {
      if (this.currentPlanId) {
        await axios
          .get(`/api/cart/plans/${this.currentPlanId}/available-days`)
          .then(r => {
            if (r.data.success) {
              this.planDays = r.data.entities
            } else {
              this.planDays = r.data.entities
              this.days = this.queryDays
            }
          })
      } else {
        this.days = this.queryDays
      }

      let fetched = false
      if (this.modify) {
        this.activeDayIndex = this.planDays.findIndex(e => {
          return e.date === this.item.timeslot.start_date
        })

        await this.fetchItems()
        fetched = true
      }

      if (!fetched && !this.dayPlanItems.length) {
        this.fetchItems()
      }
    },

    async init () {
      await this.processAvailableDays()

      this.processTimeslots()
      this.processDuration()

      if (!this.modify) {
        await this.fetchItems()
      }
    },

    async fetchCheapestRoute () {
      let params = {
        plan_id: this.currentPlanId ? this.currentPlanId : null,
        to_city_code: this.item.identifiers.city_code
      }

      if (!this.currentPlanId) {
        const startDate = this.query.date.start_date
        const endDate = this.query.date.end_date

        params = {
          ...params,
          from_city_code: this.query.origin_city.code,
          to_city_code: this.item.identifiers.city_code,
          travellers: Object.values(this.query.travellers).join('-'),
          start_date: startDate,
          end_date: endDate,
          date_type: this.query.date.date_type
        }
      }

      await axios
        .get(`/api/rides/cheapest-route`, {
          params: {
            ...params,
            date: this.currentDay.date
          }
        })
        .then(r => {
          if (r.data.success) {
            this.rides = [
              r.data.entity.routes ? r.data.entity.routes : r.data.entity
            ]
          }
        })
    },

    async fetchItems () {
      this.dayPlanLoading = true

      if (!this.currentDay || !this.currentPlanId) {
        this.dayPlanLoading = false

        return false
      }

      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/items`, {
          params: {
            date: this.currentDay.date
          }
        })
        .then(r => {
          this.dayPlanItems = []

          if (r.data.success) {
            this.dayPlanItems = r.data.entities
          }
        })

      this.dayPlanLoading = false
    },

    async fetchAvailableDaysA () {
      const id = this.modify
        ? this.id
        : this.item.id

      await axios
        .get(`/api/activities/${id}/dates`, {
          params: {
            start_date: this.queryDate.start_date,
            end_date: this.queryDate.end_date
          }
        })
        .then(r => {
          if (r.data.success) {
            for (let i = 0; i < this.days.length; ++i) {
              this.fetchRemainingHours(this.days[i].date)
                .then(value => {
                  this.planDays[i].remainingHours = value
                })

              this.planDays.push({
                ...this.days[i],
                available: r.data.entities.includes(this.days[i].date),
                remainingHours: false
              })
            }
          }
        })
    },

    async fetchRemainingHours (date) {
      return await axios
        .get(`/api/cart/plans/${this.currentPlanId}/${date}/remaining-hours`)
        .then(r => {
          return r.data.value
        })
    },

    processTravellers () {
      let activeTravellers = this.item.travellers
        .map(e => {
          return e.reference_key
        })

      let travellers = []
      this.travellers.forEach(e => {
        travellers.push({
          id: e.id,
          reference_key: e.reference_key,
          type: e.type,
          first_name: e.first_name,
          last_name: e.last_name,
          active: activeTravellers.includes(e.reference_key)
        })
      })

      this.tempTravellers = travellers
    },

    onInputTravellers (value) {
      this.tempTravellers = value
    },

    onInputTimeslot (value) {
      this.timeslot = value

      if (this.isActivity) {
        this.timeslot = {
          ...this.timeslot,
          end_time: this.timeAfterMinutes(this.timeslot.start_time, this.durationMinutes)
        }
      }
    },

    async processDuration () {
      if (this.isActivity) {
        const itemId = this.modify
          ? this.id
          : this.item.id

        await axios
          .get(`/api/activities/${itemId}/duration`)
          .then(r => {
            if (r.data.success) {
              this.durationMinutes = this.durationInMinutes(r.data.entity)
            }
          })
      }
    },

    async processTimeslots () {
      if (this.item.type === 'standard') {
        this.availableTimeslots = this.baseTimeslots.map(e => {
          return {
            start_time: e
          }
        })

        if (this.modify) {
          this.timeslot = {
            ...this.item.timeslot
          }
        } else {
          this.timeslot = {
            start_time: this.availableTimeslots[0].start_time,
            end_time: this.availableTimeslots[1].start_time
          }
        }
      } else {
        this.loadingAvailability = true

        if (this.currentDay && this.currentDay.available) {
          await this.getCurrentDayInfo()
        }

        if (this.modify) {
          this.timeslot = this.availableTimeslots.find(e => {
            return e.start_time === this.item.timeslot.start_time
          })
        }

        this.loadingAvailability = false
      }
    },

    async processAvailableDays () {
      if (this.item.type === 'standard') {
        this.planDays = this.days.map(e => {
          return {
            ...e,
            available: true
          }
        })
      } else {
        await this.fetchAvailableDaysA()
      }
    },

    onChange (items) {
      this.cartItems = items
    },

    async getCurrentDayInfo () {
      const itemId = this.modify
        ? this.id
        : this.item.id

      let success = false
      await axios
        .get(`/api/activities/${itemId}/date-info`, {
          params: {
            date: this.currentDay.date
          },
          timeout: 5000
        })
        .then(r => {
          if (r.data.success) {
            this.processDayInfo(r.data.entity)

            success = true
          }
        })
        .catch(e => {
          success = true
        })

      if (!success) {
        this.$notification.show({
          type: 'warning',
          text: 'The activity has no timeslots for this date or it may be of different type.'
        })
      }
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

      if (this.modify) {
        this.timeslot = this.availableTimeslots.find(e => {
          return e.start_time === this.item.timeslot.start_time
        })

        if (!this.timeslot || this.timeslot < 0) {
          this.timeslot = this.availableTimeslots[0]
        }
      } else {
        this.timeslot = {
          ...this.timeslot,
          end_time: this.timeAfterMinutes(this.timeslot.start_time, this.durationMinutes)
        }
      }
    },

    async onChangeDay (day) {
      if (this.activeDayIndex === day) {
        return false
      }

      this.loadingAvailability = true

      this.activeDayIndex = day

      if (this.isActivity) {
        this.cartItems = []
        this.availableTimeslots = []

        if (this.currentDay.available) {
          await this.getCurrentDayInfo()
        }
      }

      this.dayPlanItems = []

      this.dayPlanLoading = true

      await this.fetchItems()

      await this.fetchCheapestRoute()

      this.dayPlanLoading = false
      this.loadingAvailability = false
    },

    hide () {
      this.$emit('hide')
    },

    checkTravellers () {
      const count = this.tempTravellers.filter(e => {
        return e.active === true
      }).length

      if (!count) {
        this.$notification.show({
          type: 'error',
          text: 'No travellers set.'
        })

        return false
      }

      return true
    },

    validate () {
      if (!this.isCurrentDayAvailable) {
        this.$notification.show({
          type: 'error',
          text: 'Sorry, this day is not available anymore.'
        })

        return false
      }

      return this.checkTravellers() &&
        this.checkValidTimeslot() &&
        this.checkAvailable()
    },

    checkAuth () {
      if (!this.authUser) {
        this.$notification.show({
          type: 'warning',
          text: 'You are not logged in.'
        })

        return false
      }

      return true
    },

    async addToPlan () {
      this.btnLoading = true

      this.btnText = 'Validating..'

      if (!this.validate()) {
        this.btnLoading = false
        this.btnText = ''

        return false
      }

      if (!this.currentPlanId) {
        this.btnText = 'Creating plan...'

        await axios
          .post(`/api/cart/plans/store`, {
            origin_city_id: this.query.origin_city.id,
            start_date: this.planDays[0].date,
            end_date: this.planDays[this.planDays.length - 1].date,
            travellers: Object.values(this.query.travellers).join('-')
          })
          .then(r => {
            if (r.data.success) {
              this.$store.commit('setCurrentPlanId', r.data.entity_id)
            } else {
              alert('An error occured.')
            }
          })
      }

      if (this.currentPlanId) {
        this.btnText = 'Storing activity...'

        const success = await this.$store.dispatch('storeItemToPlan', {
          external_id: this.item.id.toString(),
          name: this.item.name,
          modify: this.modify,
          type: this.item.type,
          travellers: this.tempTravellers.filter(e => {
            return e.active === true
          }),
          city_id: !this.modify ? this.item.identifiers.city_id : null,
          identifiers: !this.modify ? this.item.identifiers : null,
          products: this.cartItems,
          ...this.timeslot,
          start_date: this.currentDay.date,
          end_date: this.currentDay.date
        })

        if (success) {
          if (this.modify) {
            this.btnText = 'Redirecting...'

            this.$emit('updated')
          } else {
            this.$emit('added')
          }
        } else {
          this.btnText = ''

          this.$notification.show({
            type: 'warning',
            text: 'Sorry, you cannot add multiple cities in same day.'
          })
        }
      }

      this.btnLoading = false
    },

    checkValidTimeslot () {
      const start = this.timeslot.start_time
      const end = this.timeslot.end_time

      if (start >= end) {
        this.$notification.show({
          type: 'error',
          text: 'Invalid timeslot.'
        })

        return false
      }

      return true
    },

    checkAvailable () {
      const startTime = this.timeslot.start_time
      const endTime = this.timeslot.end_time

      const items = this.dayPlanItems

      if (!items.length) {
        return true
      }

      let segments = []
      items.forEach(item => {
        segments.push({
          id: item.item_id ? item.item_id : item.id,
          start_time: item.timeslot.start_time,
          end_time: item.timeslot.end_time
        })
      })

      segments.push({
        id: this.item.id,
        start_time: startTime,
        end_time: endTime
      })

      if (this.timeslotsOverlap(segments)) {
        this.$notification.show({
          type: 'warning',
          text: 'An activity within this time already exists. (An extra hour is added between activities)'
        })

        return false
      }

      return true
    },

    timeslotsOverlap (segments) {
      segments.sort((a, b) => {
        if (a.start_time < b.start_time) {
          return -1
        } else if (a.start_time > b.start_time) {
          return 1
        }

        return 0
      })

      let i = 1
      while (i < segments.length) {
        if ((segments[i - 1].id !== segments[i].id)
          && this.timeAfterMinutes(segments[i - 1].end_time, 60) >= segments[i].start_time) {
          return true
        }

        i++
      }

      return false
    }
  }
}
</script>
