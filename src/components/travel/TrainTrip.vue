<template>
  <div class="trip train">
    <div class="title mr-5">
      <div class="name text-uppercase">
        {{ trip.name }}
      </div>
      <div class="company-id small semi-bold">
        {{ trip.code }}
      </div>
    </div>

    <div class="trip-time-wrap">
      <div class="departure">
        {{ moment(trip.departure).format('HH:mm') }}
      </div>

      <div class="trip-time">
        <div>
          <i class="fa fa-clock-o icon" />
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

      <div class="arrival">
        {{ moment(trip.arrival).format('HH:mm') }}
      </div>
    </div>

    <div class="price-list">
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
        :class="{'active': selectedPriceItem === itemIndex}"
        class="price-item"
        data-toggle="modal"
        data-target="#trainDetails"
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

    <train-details-modal
      v-if="trainModal"
      :trip="trip"
      :selected-price="trip.priceList[selectedPriceItem]"
    />
  </div>
</template>

<script>
import TrainDetailsModal from '../../components/modals/TrainDetailsModal'

export default {
  components: {
    TrainDetailsModal
  },

  props: {
    trip: {
      type: Object,
      default: () => {
      }
    }
  },

  data () {
    return {
      trainModal: false,
      selectedPriceItem: 0
    }
  },

  methods: {
    onSelectPriceItem (index) {
      this.selectedPriceItem = index

      this.trainModal = true
    }
  }
}
</script>
