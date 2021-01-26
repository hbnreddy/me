<template>
  <div class="box box-border trips rides-group">
    <div class="group-wrapper">
      <div class="rides-block">
        <ul class="rides-list">
          <li
            v-for="ride in group"
            :key="ride.id"
            class="rides-list-item"
          >
            <ride-item
              :ride="ride"
            />
          </li>
        </ul>
      </div>

      <div class="control-block">
        <p class="price">
          ${{ totalPrice.toFixed(2) }}
        </p>

        <button
          class="btn btn-orange btn-details"
          @click.prevent="expanded = !expanded"
        >
          <span v-if="expanded">{{ __('Less details') }}</span>
          <span v-else>{{ __('More details') }}</span>
        </button>

        <button
          v-if="modify"
          class="btn btn-orange btn-checkout btn-delete"
          @click.prevent="removeItems()"
        >
          {{ __('Remove') }}
        </button>

        <button
          v-else-if="addedToCart"
          class="btn btn-orange active"
        >
          {{ __('Added to cart') }}
        </button>

        <button
          v-else
          class="btn btn-orange btn-add"
          @click.prevent="addToPlan()"
        >
          {{ __('Add to plan') }}
        </button>
      </div>
    </div>

    <div v-if="expanded" class="container expanded-group">
      <div class="flights-block">
        <ul class="flights-list">
          <ride-group-item
            v-for="(rideItem, index) in group"
            :key="index"
            :index="index"
            :item="rideItem"
          />
        </ul>
      </div>
    </div>

    <add-travel-to-plan-modal
      v-if="showPopup"
      :items="group"
      @hide="showPopup = false"
    />
  </div>
</template>

<script>
import RideItem from './RideItem'
import RideGroupItem from './RideGroupItem'
import AddTravelToPlanModal from '../../components/modals/planning/AddTravelToPlanModal'

import { mapGetters } from 'vuex'
import moment from 'moment'
import {TRAVEL_GROUP_ADDED_SUCCESSFULLY} from '../../bootstrap/notify-messages'

export default {
  components: {
    AddTravelToPlanModal,
    RideItem,
    RideGroupItem
  },

  props: {
    group: {
      type: Array,
      required: true
    },

    travellers: {
      type: Array,
      default: () => []
    },

    modify: {
      type: Boolean,
      default: () => false
    }
  },

  data () {
    return {
      showPopup: false,
      expanded: false
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    groupId () {
      return this.group
        .map(e => e.id)
        .join('.')
    },

    addedToCart () {
      if (!this.group.length) {
        return false
      }

      return this.currentPlan.items.findIndex(e => {
        return e.group_id === this.groupId
      }) >= 0
    },

    totalPrice () {
      let sum = 0

      this.group.forEach(e => {
        sum += parseFloat(e.price.amount)
      })

      return sum
    }
  },

  methods: {
    removeItems () {
      this.$store.dispatch('removeTravelItems')
    },

    async addToPlan () {
      if (!this.travellers.length || !this.group.length) {
        return false
      }

      for (const e of this.group) {
        const timeslot = e.timeslot

        await this.$store.dispatch('storeItem', {
          id: e.id,
          group_id: e.group_id,
          timeslot: {
            ...timeslot,
            start_time: moment(timeslot.start_date, 'DD/MM/YYYY-HH:mm').format('HH:mm'),
            end_time: moment(timeslot.end_date, 'DD/MM/YYYY-HH:mm').format('HH:mm')
          },
          origin_city_code: e.origin_city_code,
          destination_city_code: e.destination_city_code,
          duration: e.duration,
          price: e.price,
          routing_id: e.routing_id,
          supplier: e.supplier,
          travellers: this.travellers,
          type: 'travel'
        })
      }

      this.$notification.show(TRAVEL_GROUP_ADDED_SUCCESSFULLY)
    }
  }
}
</script>
