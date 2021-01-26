<template>
  <div
    class="checkout-btn-block"
  >
    <a
      :class="{ 'btn-disabled': !hasItems }"
      class="btn-checkout"
      @click.prevent="checkout()"
    >
      {{ __('Proceed for Checkout') }}
    </a>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'

import {CHECKOUT_SUCCESS,
  CHECKOUT_TRAVELLERS_FAILED,
  HOTEL_NOT_AVAILABLE,
  TRAVEL_GROUP_NOT_AVAILABLE} from '../../../bootstrap/notify-messages'
import { validate } from '../../../mixins/validate'

export default {
  mixins: [
    validate
  ],

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    hasItems () {
      return !!this.currentPlan.items.length
    }
  },

  methods: {
    async checkout () {
      if (!this.canCheckout) {
        return false
      }

      await this.$store.dispatch('setCurrentStep', 'checkout')

      await this.$store.dispatch('verifySteps')

      const checkTravellers = this.checkTravellers()
      if (!checkTravellers) {
        return false
      }

      // const travelChecked = await this.checkoutTravel()
      const travelChecked = true
      const hotelsChecked = await this.checkoutHotel()
      const checkoutPossible = travelChecked && hotelsChecked

      if (checkoutPossible) {
        setTimeout(() => this.$store.dispatch('setCurrentStep', 'ready-for-payment'), 500)

        setTimeout(() => {
          this.$notification.show(CHECKOUT_SUCCESS)
        }, 1000)
      } else {
        await this.$store.dispatch('setCurrentStep', 'planning')
      }
    },

    async checkoutTravel () {
      const travelItems = this.currentPlan.items.filter(e => e.type === 'travel')
      if (!travelItems.length) {
        return true
      }

      const checkTravel = await this.$store.dispatch('checkoutTravelGroup')

      if (checkTravel && checkTravel.success) {
        return true
      }

      this.$store.commit('setStep', {
        key: 'travel',
        value: {
          completed: false,
          errors: ['Step not completed.']
        }
      })

      setTimeout(() => {
        this.$notification.show(TRAVEL_GROUP_NOT_AVAILABLE)
      }, 300)

      return false
    },

    async checkoutHotel () {
      const hotel = this.currentPlan.items.find(e => e.type === 'hotel')
      if (!hotel) {
        return true
      }

      const checkHotel = await this.$store.dispatch('checkoutHotel', hotel.search_key)

      if (checkHotel && checkHotel.success) {
        this.$store.dispatch('refreshItem', {
          ...checkHotel.entities[0],
          type: 'hotel'
        })

        return true
      }

      this.$store.commit('setStep', {
        key: 'stay',
        value: {
          completed: false,
          errors: ['Step not completed.']
        }
      })

      setTimeout(() => {
        this.$notification.show(HOTEL_NOT_AVAILABLE)
      }, 300)

      return false
    },

    checkTravellers () {
      if (!this.checkoutTravellers) {
        this.$notification.show(CHECKOUT_TRAVELLERS_FAILED)

        return false
      }

      return true
    }
  }
}
</script>
