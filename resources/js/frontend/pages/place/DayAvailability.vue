<template>
  <div class="details box">
    <div class="box-heading justify-content-between">
      <div>
        {{ __('Availability for the day') }}
      </div>

      <div>
        <button v-if="modify" class="btn btn-yellow d-flex justify-content-center align-items-center" @click.prevent="update()">
          <loading-circle :loading="btnLoading" :small="true" :color="'#ffffff'" class="mr-2" />
          {{ __('Modify plan') }}
        </button>

        <button v-else class="btn btn-orange d-flex justify-content-center align-items-center blob orange" @click.prevent="update()" >
          <loading-circle :loading="btnLoading" :small="true" :color="'#ffffff'" class="mr-2" />
          {{ __('Add to plan') }}
        </button>
      </div>
    </div>
    <loading-circle :loading="loading" />
    <div v-if="!available && !loading" class="p-2">
      {{ __('Nothing available this day') }}
    </div>
    <div v-if="available && !loading" class="box-body" style="overflow-x: hidden;">
      <div class="body-wrap flex-column">
        <day-availability-header :travellers="tempTravellers" :timeslots="timeslots" :active-timeslot="activeTimeslot" :has-timeslots="true" @travellers="onInputTravelers($event)" @timeslot="onInputTimeslot($event)" />
        <fare-breakup :timeslot="activeTimeslot" :travellers="tempTravellers" :currency="currency" />
      </div>
    </div>
  </div>
</template>
<script>
import FareBreakup from './FareBreakup'
import { mixin as clickaway } from 'vue-clickaway'
import DayAvailabilityHeader from './DayAvailabilityHeader'
import LoadingCircle from '../../../components/LoadingCircle'

export default {
  components: {
    LoadingCircle,
    DayAvailabilityHeader,
    FareBreakup
  },

  mixins: [
    clickaway
  ],

  props: {
    available: {
      type: Boolean,
      default: false
    },

    loading: {
      type: Boolean,
      default: false
    },

    travellers: {
      type: Array,
      default: () => []
    },

    timeslots: {
      type: Array,
      default: () => []
    },

    currency: {
      type: Object,
      required: true
    },

    timeslot: {
      type: Object | Boolean,
      default: false
    },

    modify: {
      type: Boolean,
      default: false
    },

    btnLoading: {
      type: Boolean,
      default: false
    }
  },

  data() {
    return {
      items: [],
      tempTravellers: [],
      activeTimeslot: false
    }
  },

  watch: {
    timeslots: {
      handler(values) {
        this.activeTimeslot = false

        this.tempTravellers = [
          ...this.travellers
        ]

        this.items = []

        if (values.length) {
          if (this.modify && this.timeslot) {
            this.activeTimeslot = {
              ...this.timeslot
            }
          } else {
            this.activeTimeslot = {
              ...values[0]
            }
          }

          this.emitTimeslot()
        }
      },
      immediate: true
    }
  },

  methods: {
    update() {
      this.$emit('update')
    },

    onInputTravelers(travelers) {
      const activeTravellers = travelers
        .filter(e => {
          return e.active === true
        })

      if (activeTravellers.length) {
        let available = true

        activeTravellers
          .forEach(e => {
            if (!this.activeTimeslot[e.type] || !this.activeTimeslot[e.type].availability) {
              available = false
            }
          })

        if (!available) {
          this.$notification.show({
            type: 'warning',
            text: 'There are no more seats.'
          })

          return false
        }
      }

      this.tempTravellers = [
        ...travelers
      ]

      this.$emit('travellers', this.tempTravellers)

      this.processItems()
    },

    onInputTimeslot(index) {
      this.activeTimeslot = {
        ...this.timeslots[index]
      }

      this.emitTimeslot()

      this.processItems()
    },

    emitTimeslot() {
      this.$emit('timeslot', {
        start_time: this.activeTimeslot.start_time,
        end_time: this.activeTimeslot.end_time
      })
    },

    processItems() {
      this.items = []

      let items = {
        //
      }
      this.tempTravellers.forEach(traveller => {
        if (traveller.active && this.activeTimeslot[traveller.type]) {
          const product = this.activeTimeslot[traveller.type]

          items[product.product_id] = items[product.product_id] ? items[product.product_id] : {
            travellers: []
          }
          items[product.product_id] = {
            ...items[product.product_id],
            product_id: product.product_id,
            product_type: product.product_type,
            travellers: [
              ...items[product.product_id].travellers,
              traveller
            ]
          }
        }
      })

      this.items = Object.values(items)

      if (this.items.length) {
        this.$emit('change', [
          ...this.items
        ])
      }
    }
  }
}

</script>
