<template>
  <div class="day-column">
    <div
      v-for="(item, itemIndex) in items"
      :key="itemIndex"
      :class="[
        item.type,
        'event-' + duration(item.timeslot)
      ]"
      :style="height(item.timeslot)"
      class="vuecal__cel-event"
    >
      <cancel-activity-modal
        v-if="1 === 0 && showCancelActivityPopup"
        :activity="item"
        @hide="showCancelActivityPopup = false"
      />

      <div v-if="['standard', 'activity'].includes(item.type)">
        <activity-card
          :item="item"
          @remove="removeItem(item.id, item.type)"
        />
      </div>

      <div
        v-if="item.type === 'driving'"
        class="position-relative"
      >
        <driving-card
          :item="item"
        />
      </div>

      <div v-else-if="item.type === 'stay'">
        <hotel-card
          :item="item"
        />
      </div>

      <div v-else-if="item.type === 'flight'">
        <flight-card
          :item="item"
        />
      </div>
    </div>
  </div>
</template>

<script>
import CancelActivityModal from '../../../modals/CancelActivityModal'
import ActivityCard from './item-types/ActivityCard'
import DrivingCard from './item-types/DrivingCard'
import HotelCard from './item-types/HotelCard'
import FlightCard from './item-types/FlightCard'

import {mapGetters} from 'vuex'
import axios from 'axios'

export default {
  components: {
    FlightCard,
    HotelCard,
    DrivingCard,
    ActivityCard,
    CancelActivityModal
  },

  props: {
    items: {
      type: Array,
      default: () => []
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ])
  },

  methods: {
    async removeItem (id, type) {
      await axios
        .post(`/api/cart/plans/${this.currentPlanId}/items/${id}/remove`, { type })
        .then(r => {
          if (r.data.success) {
            const index = this.items.findIndex(e => {
              return e.id === id
            })

            if (index >= 0) {
              this.items.splice(index, 1)
            }
          }
        })
    },

    duration (timeslot) {
      return parseInt(timeslot.end_time) - parseInt(timeslot.start_time)
    },

    height (timeslot) {
      let time = parseInt(timeslot.start_time)

      if (time === 0) {
        time = 24
      }

      time -= 6

      if (time < 0) {
        return false
      }

      return {
        top: 'calc(90px * ' + time + ')',
        height: 'calc(90px * ' + this.duration(timeslot) + ')'
      }
    }
  }
}
</script>
