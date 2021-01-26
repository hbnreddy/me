<template>
  <div class="checkout-steps">
    <planning v-if="isStep(1)" />

    <traveller-details v-if="isStep(2)" />

    <checkout v-if="isStep(3)" />
  </div>
</template>

<script>
import Planning from './Planning'
import TravellerDetails from './travellers-form/TravellerDetails'
import {mapGetters} from 'vuex'
import Checkout from './Checkout'

export default {
  components: {
    Planning,
    TravellerDetails,
    Checkout
  },

  computed: {
    ...mapGetters([
      'checkoutStep'
    ])
  },

  methods: {
    isStep (index) {
      return this.checkoutStep === index
    }
  }
}
</script>
