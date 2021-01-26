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
        class="modal-dialog modal-xl"
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
              v-if="activeDate"
              :days="days"
              :loading="daysRowLoading"
              :active-date="activeDate"
              @change="changeDate($event)"
            />

            <div class="content-wrapper">
              <day-plan
                :loading="dayPlanLoading"
                :items="allItems"
                :currency="currentCurrency"
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
import StandardDayAvailability from '../../../views/place/StandardDayAvailability'
import DaysRow from '../../../views/place/DaysRow'
import DayAvailability from '../../../views/place/DayAvailability'
import DayPlan from '../../../views/place/DayPlan'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import {duration} from '../../../mixins/duration'
import {query} from '../../../mixins/query'
import {
  ACTIVITY_TIMESLOT_ALREADY_EXISTS,
  DAY_NOT_AVAILABLE, INVALID_TIMESLOT,
  NO_TIMESLOTS_AVAILABLE,
  NOT_LOGGED_IN, SINGLE_DAY_MULTIPLE_CITIES, TRAVELLERS_NOT_SET,
  UNKNOWN_ERROR_OCCURED
} from '../../../bootstrap/notify-messages'

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
      btnText: '',
      btnLoading: false,
      dayPlanItems: [],
      dayPlanLoading: false,
      tempTravellers: [],
      durationMinutes: false,
      loadingAvailability: false,
      days: {
        //
      },
      cartItems: [],
      availableTimeslots: [],
      timeslot: {
        start_time: false,
        end_time: false
      },
      activeDate: false
    }
  },

  computed: {
    ...mapGetters([
      'currentCurrency',
      'plans',
      'currentPlan',
      'user',
      'query',
      'originCity',
      'currencies',
      'cart'
    ]),

    itemId () {
      return this.modify
        ? this.id
        : this.item.id
    },

    allItems () {
      if (!this.currentPlan) {
        return []
      }

      const items = this.currentPlan.items
        .filter(e => {
          return e.timeslot.start_date === this.activeDate && !['hotel'].includes(e.type)
        })
        .sort((a, b) => {
          if (a.timeslot.start_time === b.timeslot.start_time) {
            return 0
          }

          return a.timeslot.start_time > b.timeslot.start_time ? 1 : -1
        })

      const hotels = this.currentPlan.items
        .filter(e => {
          return e.timeslot.start_date === this.activeDate && ['hotel'].includes(e.type)
        })

      return [
        ...items,
        ...hotels
      ]
    },

    isCurrentDayAvailable () {
      return !!(this.currentDay && this.currentDay.available && this.availableTimeslots.length)
    },

    isActivity () {
      return this.item.type === 'activity'
    },

    currentDay () {
      return this.days[this.activeDate]
    }
  },

  created () {
    if (this.modify) {
      this.activeDate = this.item.timeslot.start_date
      this.tempTravellers = this.currentPlan.travellers
        .map(e => {
          return {
            id: e.id,
            reference_key: e.reference_key,
            type: e.type,
            first_name: e.first_name,
            last_name: e.last_name,
            active: this.item.travellers.includes(e.id)
          }
        })
    } else {
      this.tempTravellers = this.queryTravellers.map(e => {
        return {
          ...e,
          active: true
        }
      })
    }

    this.initialize()
  },

  methods: {
    async createPlan () {
      return await this.$store.dispatch('createPlan', {
        origin_city_id: this.originCity.id,
        start_date: this.queryDays[0].date,
        end_date: this.queryDays[this.queryDays.length - 1].date,
        travellers: Object.values(this.query.travellers).join(',')
      })
    },

    async initialize () {
      await this.processAvailableDays()

      this.processTimeslots()
      this.processDuration()

      if (!this.modify) {
        await this.fetchItems()
      }
    },

    async fetchCheapestRoute () {
      let params = {
        planId: this.currentPlanId,
        arrival_city_code: this.item.city_code
      }

      if (!this.currentPlanId) {
        const startDate = this.query.start_date
        const endDate = this.query.end_date

        params = {
          ...params,
          departure_city_code: this.query.origin_city.code,
          arrival_city_code: this.item.city_code,
          travellers: Object.values(this.query.travellers).join(','),
          start_date: startDate,
          end_date: endDate
        }
      }
    },

    async fetchItems () {
      this.dayPlanItems = []
      this.dayPlanLoading = true

      if (!this.currentDay || !this.currentPlanId) {
        this.dayPlanLoading = false

        return false
      }

      this.dayPlanItems = await this.$store.dispatch('', {
        planId: this.currentPlanId,
        date: this.currentDay.date
      })

      this.dayPlanLoading = false
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
        this.durationMinutes = this.durationInMinutes(this.$store.dispatch('fetchItemDuration', this.itemId))
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
        this.queryDays.forEach(day => {
          this.days = {
            ...this.days,
            [day.date]: {
              ...day,
              available: true,
              city_id: this.item.city_id
            }
          }

          if (!this.activeDate) {
            this.changeDate(day.date)
          }
        })
      } else {
        for (const day of this.queryDays) {
          const available = await this.$store.dispatch('checkDayAvailability', {
            itemId: this.item.id,
            date: day.date
          })

          this.days = {
            ...this.days,
            [day.date]: {
              ...day,
              available
            }
          }

          if (!this.activeDate) {
            this.changeDate(day.date)
          }
        }
      }
    },

    onChange (items) {
      this.cartItems = items
    },

    async getCurrentDayInfo () {
      let success = false

      const activityDateInfo = await this.$store.dispatch('fetchActivityDateInfo', this.itemId)

      this.processDayInfo(activityDateInfo)

      if (!success) {
        this.$notification.show(NO_TIMESLOTS_AVAILABLE)
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

    async changeDate (date) {
      if (this.activeDate === date) {
        return false
      }

      this.loadingAvailability = true

      this.activeDate = date

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
        this.$notification.show(TRAVELLERS_NOT_SET)

        return false
      }

      return true
    },

    validate () {
      if (!this.isCurrentDayAvailable) {
        this.$notification.show(DAY_NOT_AVAILABLE)

        return false
      }

      return this.checkTravellers() &&
          this.checkValidTimeslot() &&
          this.checkAvailable() &&
          this.uniqueCities()
    },

    async storeItem () {
      const referenceKeys = this.tempTravellers.filter(e => {
        return e.active === true
      }).map(e => e.reference_key)

      const travellers = this.currentPlan.travellers.filter(e => {
        return referenceKeys.includes(e.reference_key)
      }).map(e => e.id)

      return await this.$store.dispatch('storeItem', {
        id: this.item.id.toString(),
        name: this.item.name,
        type: this.item.type,
        city_id: this.item.city_id,
        city_code: this.item.city_code,
        place_id: this.item.place_id,
        travellers,
        timeslot: {
          ...this.timeslot,
          start_date: this.currentDay.date,
          end_date: this.currentDay.date
        }
      })
    },

    checkAuth () {
      if (!this.user) {
        this.$notification.show(NOT_LOGGED_IN)

        return false
      }

      return true
    },

    async addToPlan () {
      this.btnLoading = true

      if (!this.validate()) {
        this.btnLoading = false
        this.btnText = ''

        return false
      }

      this.btnText = 'Saving activity...'

      let plan = false
      if (!this.currentPlan) {
        plan = await this.createPlan()

        if (!plan) {
          this.$notification.show(UNKNOWN_ERROR_OCCURED)

          return false
        }

        this.$store.dispatch('storePlan', {
          ...plan,
          currentStep: 'planning',
          query: this.$route.query,
          steps: {
            activities: {
              complete: false,
              errors: []
            },
            travel: {
              complete: false,
              errors: []
            },
            hotels: {
              complete: false,
              errors: []
            },
            travellers: {
              complete: false,
              errors: []
            }
          }
        })
      }

      const success = await this.storeItem()

      if (success) {
        if (this.modify) {
          this.btnText = 'Item updated'

          this.$emit('updated')
        } else {
          this.btnText = 'Item created'

          this.$emit('added')
        }
      } else {
        this.btnText = ''

        this.$notification.show(SINGLE_DAY_MULTIPLE_CITIES)
      }

      this.btnLoading = false
    },

    checkValidTimeslot () {
      const start = this.timeslot.start_time
      const end = this.timeslot.end_time

      if (start >= end) {
        this.$notification.show(INVALID_TIMESLOT)

        return false
      }

      return true
    },

    checkAvailable () {
      const startTime = this.timeslot.start_time
      const endTime = this.timeslot.end_time

      let items = this.allItems

      if (!items.length) {
        return true
      }

      items = items.filter(item => !['hotel'].includes(item.type))

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
        this.$notification.show(ACTIVITY_TIMESLOT_ALREADY_EXISTS)

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
            && this.timeAfterMinutes(segments[i - 1].end_time, 59) >= segments[i].start_time) {
          return true
        }

        i++
      }

      return false
    },

    /**
       * @returns {boolean}
       */
    uniqueCities () {
      const allItems = this.allItems.filter(item => {
        return !['hotel'].includes(item.type)
      })

      if (allItems.length && allItems[0].city_id !== this.currentDay.city_id) {
        this.$notification.show(SINGLE_DAY_MULTIPLE_CITIES)

        return false
      }

      return true
    }
  }
}
</script>
