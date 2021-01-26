<template>
  <div>
    <navbar :auth-user="data.user || false" />

    <div class="booking">
      <checkout-steps />

      <trip-estimate />
    </div>
  </div>
</template>

<script>
import Navbar from '../../components/Navbar'
import TripEstimate from './components/estimate/TripEstimate'
import CheckoutSteps from './components/CheckoutSteps'
import axios from 'axios'

export default {
  components: {
    Navbar,
    CheckoutSteps,
    TripEstimate
  },

  props: {
    data: {
      type: Object,
      default: () => {
      }
    }
  },

  data () {
    return {
      activitiesPrice: 0,
      flightsPrice: 0,
      hotelsPrice: 0,
      loading: false
    }
  },

  created () {
    this.$store.commit('setCurrentPlanId', this.data.current_plan_id)
    this.fetchTravellers()

    if (!this.authUser && this.data.user) {
      this.$store.commit('setAuthUser', this.data.user)
    }

    this.$store.commit('setQuery', this.data.query)
  },

  methods: {
    async fetchTravellers () {
      await axios
        .get(`/api/cart/plans/${this.data.current_plan_id}/travellers`)
        .then(r => {
          if (r.data.success) {
            this.$store.commit('setTravellers', r.data.entities)
          }
        })
    }
  }
}
</script>
