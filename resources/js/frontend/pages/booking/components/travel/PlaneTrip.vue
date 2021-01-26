<template>
  <div class="trip plane">
    <div class="logo mr-3">
      <img
        :src="`../images/travel/plane/${trip.image}`"
        alt="logo"
        class="img-fluid"
      >
    </div>

    <div class="title mr-5">
      <div class="name">
        {{ trip.name }}
      </div>
      <div class="company-id small semi-bold">
        {{ trip.code }}
      </div>
    </div>

    <div class=" flex-fill d-flex">
      <div class="trip-label">
        <div class="departure">
          <span class="clock">{{ moment(trip.departure).format('HH:mm') }}</span>

          <span class="small text-muted">
            <span class="semi-bold">{{ trip.departure_code }}</span>
            {{ trip.departure_city }}, {{ trip.departure_country }} |
            <span class="semi-bold">{{ moment(trip.departure).format('dd MMM') }}</span>
          </span>
        </div>

        <div class="arrival">
          {{ moment(trip.arrival).format('HH:mm') }}

          <span class="small text-muted">
            <span class="semi-bold">{{ trip.arrival_code }}</span>
            {{ trip.arrival_city }}, {{ trip.arrival_country }} |
            <span class="semi-bold">{{ moment(trip.arrival).format('dd MMM') }}</span>
          </span>
        </div>
      </div>
    </div>

    <div class="trip-time">
      <div>
        <span class="semi-bold">21</span> hr
        <span class="semi-bold">12</span> min
      </div>

      <div
        v-if="trip.stops"
        class="stops"
      >
        {{ trip.stops }} {{ __('stops') }}
      </div>
      <div
        v-else
        class="stops"
      >
        {{ __('direct') }}
      </div>
    </div>

    <div class="price-list flex-fill">
      <div class="trip-type">
        <span
          :class="'badge-' + trip.trip_type_code.toLowerCase()"
          class="badge"
        >
          {{ trip.trip_type_code }}
        </span>
      </div>

      <div
        v-for="(item, itemIndex) in trip.priceList"
        :key="itemIndex"
        :class="{'active': chosen && selectedPriceItem === itemIndex}"
        class="price-item"
        data-toggle="modal"
        @click="onSelectPriceItem(itemIndex)"
      >
        <div class="price">
          $ {{ item.price }}
        </div>
        <div class="package text-capitalize">
          {{ item.package }}
        </div>
      </div>
    </div>

    <plane-details-modal v-if="planeModal" :trip="computedTrip" />
  </div>
</template>

<script>
import PlaneDetailsModal from '../../../../modals/PlaneDetailsModal'

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
    computedTrip () {
      return {
        ...this.trip,
        price: this.trip.priceList[this.selectedPriceItem]
      }
    }
  },

  methods: {
    onSelectPriceItem (index) {
      this.selectedPriceItem = index

      this.$emit('chosen', this.trip.id)

      this.planeModal = true
    }
  }
}
</script>
