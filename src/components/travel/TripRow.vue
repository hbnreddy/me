<template>
  <div class="trip plane">
    <img
      :src="airlineLogo"
      alt="logo"
      class="img-fluid mr-3"
    >

    <div class="title mr-5">
      <div class="name">
        {{ trip.segments[0].flight_id.code }}-{{ trip.segments[0].flight_id.number }}
      </div>

      <div class="company-id small semi-bold">
        {{ trip.id }}
      </div>
    </div>

    <div class=" flex-fill d-flex">
      <div class="trip-label">
        <div class="departure">
          <span class="clock">{{ moment(trip.timeslot.start_date, 'DD/MM/YYYY-HH:mm').format('HH:mm') }}</span>

          <span class="small text-muted">
            <span class="semi-bold">{{ trip.origin_code }}</span>

            <span class="clock">{{ moment(trip.timeslot.start_date, 'DD/MM/YYYY-HH:mm').format('dd MMM') }}</span>
            <span class="semi-bold">20 JAN</span>
          </span>
        </div>

        <div class="arrival">
          <span class="clock">{{ moment(trip.timeslot.end_date, 'DD/MM/YYYY-HH:mm').format('HH:mm') }}</span>

          <span class="small text-muted">
            <span class="semi-bold">{{ trip.destination_code }}</span>

            <span class="clock">{{ moment(trip.timeslot.end_date, 'DD/MM/YYYY-HH:mm').format('dd MMM') }}</span>
          </span>
        </div>
      </div>
    </div>

    <div class="flex-fill d-flex justify-content-end">
      <div class="price-selected text-center">
        <div>{{ trip.price.currency }} {{ trip.price.amount }}</div>

        <div>{{ __('Base fare') }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  props: {
    trip: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ]),

    airlineLogo () {
      return `http://www.travelfusion.com/images/logos/${this.trip.supplier}.gif`
    }
  }
}
</script>
