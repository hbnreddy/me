<template>
  <div
    :class="{ 'active-day': active === true }"
    class="card"
    @click.prevent="$emit('selected')"
  >
    <div
      class="card-body"
      style="padding: 6px 0; text-align: center;"
    >
      <div class="date">
        <div
          class="card-text day"
          style="font-size: 18px; text-transform: capitalize; font-weight: 500 !important;"
        >
          {{ day.day }} {{ day.month.substr(0, 3) }}
        </div>

        <div
          class="card-text occupied"
          style="font-weight: 500;"
        >
          <span v-if="remainingHours !== false">
            {{ remainingHours }} / 24 hrs {{ __('left') }}
          </span>

          <loading-cirscle
            v-if="remainingHours === false"
            :loading="remainingHours === false"
            :small="true"
          />
        </div>

        <div v-if="availableFlag">
          <div
            v-if="available"
            style="color: darkgreen;"
          >
            {{ __('Available') }}
          </div>

          <div
            v-else
            style="color: red;"
          >
            {{ __('Not available') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingCircle from '../../components/loading/LoadingCircle'
import {mapGetters} from 'vuex'
import moment from 'moment'

export default {
  components: {
    LoadingCircle
  },

  props: {
    day: {
      type: Object,
      required: true
    },

    availableFlag: {
      type: Boolean,
      default: true
    },

    active: {
      type: Boolean,
      required: true
    },

    available: {
      type: Boolean,
      required: true
    }
  },

  data () {
    return {
      hoursInDay: 24
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    remainingHours () {
      if (! this.currentPlan) {
        return this.hoursInDay
      }

      const items = this.currentPlan.items.filter(item => {
        return item.timeslot.start_date === this.day.date
      })

      let sum = 0
      items.forEach(item => {
        sum += this.duration(item.timeslot) + 1
      })

      return Math.ceil(this.hoursInDay - sum)
    }
  },

  methods: {
    duration (timeslot) {
      if (!timeslot.start_time) {
        return 0
      }

      let startDate = timeslot.start_date
      let endDate = timeslot.end_date

      if (startDate.indexOf(':') === -1) {
        startDate = `${startDate} ${timeslot.start_time}`
        endDate = `${endDate} ${timeslot.end_time}`
      }

      endDate = moment(endDate)
      startDate = moment(startDate)

      return Math.abs(endDate.diff(startDate, 'hours', true))
    },
  }
}
</script>
