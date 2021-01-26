<template>
  <div class="item-card flight-card">
    <div class="item-body">
      <div class="top-side">
        <div class="mr-1 align-self-start">
          <i class="fa fa-plane icon" />

          <div v-if="item.timeslot.start_time" class="title text-uppercase">
            {{ item.origin_code }} - {{ item.destination_code }}
            {{ item.timeslot.start_time }} - {{ item.timeslot.end_time }}
          </div>
        </div>

        <div class="vuecal__event-content d-flex">
          <div
            v-for="(person, personIndex) in computedTravellers"
            v-if="personIndex < 2"
            :key="personIndex"
            class="person"
          >
            {{ initials(person) }}
          </div>

          <div
            v-if="remainingTravellersCount(computedTravellers) > 0"
            class="person person-count"
          >
            +&nbsp;{{ remainingTravellersCount(computedTravellers) }}
          </div>
        </div>

        <p class="price">
          {{ item.price.currency }} {{ item.price.amount }}
          <span v-if="item.price.final">(Final)</span>
        </p>
      </div>

      <div v-if="item.status" class="bottom-side">
        <div class="actions">
          <div
            v-if="loadingStatus"
            :class="'in-progress'"
            class="status"
          >
            Loading...
          </div>

          <div
            v-else
            :class="'in-progress'"
            class="status"
          >
            {{ item.status }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { route } from '../../../mixins/route'
import {print} from '../../../mixins/print'

export default {
  mixins: [
    route,
    print
  ],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      statusInterval: false,
      loadingStatus: false,
      statusReceived: false
    }
  },

  computed: {
    ...mapGetters([
      'currentTravellers'
    ]),

    computedTravellers () {
      return this.currentTravellers.filter(e => {
        return this.item.travellers.includes(e.id)
      })
    }
  },

  mounted () {
    if (this.item.status && !this.statusReceived) {
      this.statusInterval = setInterval(() => {
        this.getBookingStatus()

        if (this.statusReceived) {
          clearInterval(this.statusInterval)
        }
      }, 5000)
    }
  },

  beforeDestroy () {
    clearInterval(this.statusInterval)
  },

  methods: {
    async getBookingStatus () {
      this.loadingStatus = true

      const status = await this.$store.dispatch('getBookingStatus', {
        id: this.item.booking_id,
        type: 'travel'
      })

      const timeout = setTimeout(() => {
        this.loadingStatus = false
        clearInterval(timeout)
      }, 2000)
    }
  }
}
</script>
