<template>
  <div class="traveller-details">
    <p class="title">
      {{ __('Travellers Details') }}
    </p>

    <div class="box box-border">
      <travellers-tabs
        :travellers="tempTravellers"
        :completed="completed"
        :active-index="activeTravellerIndex"
        @change="activeTravellerIndex = $event"
        @store="storeTravellers()"
      />

      <traveller-form
        v-if="tempTravellers[activeTravellerIndex]"
        :traveller="tempTravellers[activeTravellerIndex]"
        :completed="completed[tempTravellers[activeTravellerIndex].id]"
        :main="isMainCustomer(tempTravellers[activeTravellerIndex])"
        :loading="loading"
        @update="updateTraveller($event)"
      />
    </div>
  </div>
</template>

<script>
import TravellersTabs from '../../../components/travellers/TravellersTabs'
import TravellerForm from '../../../components/travellers/TravellerForm'
import {mapGetters} from 'vuex'
import {validate} from '../../../mixins/validate'
import {
  CHECKOUT_TRAVELLERS_LACK,
  CHECKOUT_TRAVELLERS_SET,
  TRAVELLER_EMAIL_EXISTS,
  TRAVELLER_INFORMATION_STORED
} from '../../../bootstrap/notify-messages'

export default {
  components: {
    TravellersTabs,
    TravellerForm
  },

  mixins: [
    validate
  ],

  data () {
    return {
      loading: false,
      type: 'success',
      completed: {
        //
      },
      activeTravellerIndex: 0,
      tempTravellers: [],
      mainCustomerSet: false
    }
  },

  computed: {
    ...mapGetters([
      'user',
      'currentPlan'
    ])
  },

  created () {
    this.checkAuth()

    this.tempTravellers = [
      ...this.currentPlan.travellers
    ]

    this.checkCompletedForms()

  },

  methods: {
    async storeTravellers () {
      if (!this.checks()) {
        return false
      }

      for (const traveller of this.tempTravellers) {
        await this.$store.dispatch('storeTraveller', traveller)
      }

      this.$store.dispatch('verifySteps')

      this.$notification.show(CHECKOUT_TRAVELLERS_SET)
    },

    checks () {
      if (!this.checkAuth()) {
        return false
      }

      const allSet = this.tempTravellers.every(e => !!e.email)
      if (!allSet) {
        this.$notification.show(CHECKOUT_TRAVELLERS_LACK)
      }

      if (!this.checkMainCustomer(this.tempTravellers)) {
        return false
      }

      return true
    },

    checkCompletedForms () {
      this.tempTravellers.forEach(e => {
        this.completed = {
          ...this.completed,
          [e.id]: !!e.email
        }
      })
    },

    setMainCustomer () {
      this.tempTravellers.forEach(e => {
        e.main_customer = false
      })

      this.tempTravellers[this.activeTravellerIndex].main_customer = true
    },

    async updateTraveller (next = false) {
      this.loading = true

      if (this.emailExists()) {
        this.$notification.show(TRAVELLER_EMAIL_EXISTS)
        this.loading = false

        return false
      }

      this.checkCompletedForms()

      this.$notification.show(TRAVELLER_INFORMATION_STORED)

      if (next && this.activeTravellerIndex + 1 < this.tempTravellers.length) {
        this.activeTravellerIndex++
      }

      this.loading = false
    },

    emailExists () {
      let found = false
      let emails = []

      this.tempTravellers.forEach(e => {
        if (emails.includes(e.email)) {
          found = true
        }

        if (e.email) {
          emails.push(e.email)
        }
      })

      return found
    }
  }
}
</script>
