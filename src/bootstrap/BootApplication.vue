<template>
  <div class="d-none" />
</template>

<script>
import {mapGetters} from 'vuex'

export default {
  computed: {
    ...mapGetters([
      'user',
      'query',
      'orders',
      'currentStep',
      'currentPlan'
    ])
  },

  watch: {
    $route: {
      handler () {
        this.bootRouteQuery()
      }
    }
  },

  created () {
    this.bootAuth()
    this.bootCheckout()
    this.bootRouteQuery()
    this.bootEntity()
  },

  methods: {
    async bootCheckout () {
      await this.$store.dispatch('initCart')

      if (this.currentPlan) {
        let step = this.currentPlan.currentStep
        if (step === 'checkout') {
          step = 'planning'
        }

        await this.$store.dispatch('setCurrentStep', step)
        this.$store.dispatch('verifySteps')
      }
    },

    bootRouteQuery () {
      if (Object.keys(this.query).length) {
        return false
      }

      if (this.$route.name !== 'welcome' &&
        !this.currentPlan &&
        !Object.keys(this.$route.query).length &&
        !this.orders.length
      ) {
        this.$router.replace({
          name: 'welcome',
          params: {
            //
          },
          query: {
            //
          }
        })

        return false
      }

      let query = null

      if (this.currentPlan) {
        query = this.currentPlan.query
      } else {
        query = this.$route.query
      }

      if (!Object.keys(query).length) {
        return false
      }

      if (query.currency) {
        this.$store.commit('setCurrentCurrency', query.currency)
      }

      this.$store.dispatch('fetchOriginCity', query.origin_city_id)
      this.$store.commit('processQuery', query)
    },

    bootEntity () {
      this.$store.dispatch('fetchNotifications')
      this.$store.dispatch('fetchLocales')
      this.$store.dispatch('fetchCurrencies')
      this.$store.dispatch('fetchThemes')
    },

    async bootAuth () {
      await this.$store.dispatch('checkAuth')

      if (this.user) {
        this.$store.dispatch('fetchOrders')
      }
    }
  }
}
</script>
