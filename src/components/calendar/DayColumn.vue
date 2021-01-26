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
      <component
        :is="itemType[item.type]"
        :item="item"
        @remove="showCancelActivityPopup = true"
      />

      <cancel-activity-modal
        v-if="showCancelActivityPopup"
        :item="item"
        @hide="showCancelActivityPopup = false"
        @cancel="removeItem(item.id)"
      />
    </div>
  </div>
</template>

<script>
import CancelActivityModal from '../modals/cancellation/CancelActivityModal'
import ActivityCard from './items/ActivityCard'
import DrivingCard from './items/DrivingCard'
import HotelCard from './items/HotelCard'
import TravelCard from './items/TravelCard'

import moment from 'moment'
import {ITEM_REMOVED_SUCCESSFULLY} from '../../bootstrap/notify-messages'

export default {
  components: {
    TravelCard,
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

  data () {
    return {
      showCancelActivityPopup: false,
      itemType: {
        standard: 'activity-card',
        activity: 'activity-card',
        travel: 'travel-card',
        hotel: 'hotel-card'
      }
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

    height (timeslot) {
      if (!timeslot.start_time || !timeslot.end_time) {
        return {
          top: 'calc(180px * 23)',
          height: 'calc(180px * 1)'
        }
      }

      const startHour = parseInt(timeslot.start_time.split(':')[0])
      const startMinute = parseInt(timeslot.start_time.split(':')[1]) / 60

      let time = startHour + startMinute

      if (time === 0) {
        time = 24
      }

      time -= 6

      if (time < 0) {
        return false
      }

      return {
        top: 'calc(180px * ' + time + ')',
        height: 'calc(180px * ' + this.duration(timeslot) + ')'
      }
    },

    async removeItem (itemId) {
      const success = await this.$store.dispatch('removeItem', itemId)

      if (success) {
        this.$notification.show(ITEM_REMOVED_SUCCESSFULLY)
      }

      this.showCancelActivityPopup = false
    }
  }
}
</script>
