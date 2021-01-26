<template>
  <div class="ride-item">
    <div class="start">
      <div class="airline-logo">
        <img
          :src="airlineLogo"
          :alt="ride.supplier"
          class="logo-img"
        />
      </div>

      <div class="info-block info-timing">
        <div class="timing">
          {{ moment(ride.timeslot.start_date, 'DD/MM/YYYY-HH:mm').format('HH:mm') }}
          - {{ moment(ride.timeslot.end_date, 'DD/MM/YYYY-HH:mm').format('HH:mm') }}
        </div>
        <div class="airline-name">{{ ride.supplier }}</div>
      </div>
    </div>

    <div class="info-block">
      <div v-if="ride.stops" class="stops">{{ ride.stops }} stop<span v-if="ride.stops > 1">s</span></div>
      <div v-else class="stops bold">Direct</div>

      <div v-if="ride.stops && stopsString" class="stops-list">
        {{ stopsString }}
      </div>
    </div>

    <div class="info-block">
      <div class="duration">
        <span v-if="hours">{{ hours }}h</span>
        <span v-if="minutes">{{ minutes }}m</span>
      </div>

      <div class="path">
        {{ ride.origin_code }}, {{ ride.destination_code }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    ride: {
      type: Object,
      required: true
    }
  },

  computed: {
    airlineLogo () {
      return `http://www.travelfusion.com/images/logos/${this.ride.supplier}.gif`
    },

    hours () {
      return parseInt(this.ride.duration / 60)
    },

    minutes () {
      return this.ride.duration - (60 * this.hours)
    },

    stopsString () {
      const segments = this.ride.segments
      segments.splice(0, 1)

      return segments.map(e => {
        return e.destination.code
      }).join(', ')
    }
  }
}
</script>
