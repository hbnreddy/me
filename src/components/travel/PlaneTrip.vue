<template>
  <div class="trip plane">
    <div class="logo mr-3">
      <img
        v-if="trip.image"
        :src="`../images/travel/plane/${trip.image}`"
        alt="logo"
        class="img-fluid"
      >
    </div>

    <div class="title mr-5">
      <div class="name">
        {{ trip.routes[0].operator.name }}
      </div>

      <div class="company-id small semi-bold">
        {{ trip.flight_id.code }}-{{ trip.flight_id.number }}
      </div>
    </div>

    <div class="flex-fill d-flex">
      <div
        class="trip-label"
      >
        <div class="departure">
          <span class="clock">{{ moment(departureRoute.departure.date).format('HH:mm') }}</span>

          <span class="small text-muted">
            <span class="semi-bold">{{ departureRoute.departure.airport_code }}</span>
            {{ trip.departure.city_name }}, {{ trip.departure.country_name }} |
            <span class="semi-bold">{{ moment(departureRoute.departure.date).format('dd MMM') }}</span>
          </span>
        </div>

        <div class="arrival">
          {{ moment(arrivalRoute.arrival.date).format('HH:mm') }}

          <span class="small text-muted">
            <span class="semi-bold">{{ arrivalRoute.arrival.airport_code }}</span>
            {{ trip.arrival.city_name }}, {{ trip.arrival.country_name }}
            <span class="semi-bold">{{ moment(arrivalRoute.arrival.date).format('dd MMM') }}</span>
          </span>
        </div>
      </div>
    </div>

    <div class="trip-time">
      <div>
        <span v-if="hours" class="semi-bold">{{ hours }}</span> hr
        <span v-if="minutes" class="semi-bold">{{ minutes }}</span> min
      </div>

      <div
        v-if="trip.stops_count"
        class="stops"
      >
        {{ trip.stops_count }} {{ __('stops') }}
      </div>

      <div
        v-else
        class="stops"
      >
        {{ __('direct') }}
      </div>
    </div>

    <div class="price-list flex-fill">
      <div
        class="price-item"
        data-toggle="modal"
      >
        <div class="price">
          {{ currencySymbol(trip.price.currency) }}{{ trip.price.amount }}
        </div>

        <div class="package text-capitalize">
          {{ departureRoute.class.code }} ({{ departureRoute.class.name }})
        </div>
      </div>
    </div>

    <plane-details-modal v-if="planeModal" :trip="computedTrip" />
  </div>
</template>

<script>
import PlaneDetailsModal from '../../../components/modals/planning/AddTravelToPlanModal'

export default {
  components: {
    PlaneDetailsModal
  },

  props: {
    trip: {
      type: Object,
      default: () => {
      }
    },

    chosen: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      planeModal: false,
      selectedPriceItem: 0
    }
  },

  computed: {
    departureRoute () {
      return this.trip.routes[0]
    },

    arrivalRoute () {
      return this.trip.routes[this.trip.routes.length - 1]
    },

    hours () {
      return parseInt(this.trip.duration / 60)
    },

    minutes () {
      return this.trip.duration - (60 * this.hours)
    },

    computedTrip () {
      return {
        ...this.trip,
        price: this.trip.priceList[this.selectedPriceItem]
      }
    }
  },

  methods: {
    currencySymbol (name) {
      switch (name.toLowerCase()) {
      case 'eur': {
        return '€'
      }
      case 'usd': {
        return '$'
      }
      case 'gbp': {
        return '£'
      }
      default: {
        return '$'
      }
      }
    },

    onSelectPriceItem (index) {
      this.selectedPriceItem = index

      this.$emit('chosen', this.trip.id)

      this.planeModal = true
    }
  }
}
</script>
