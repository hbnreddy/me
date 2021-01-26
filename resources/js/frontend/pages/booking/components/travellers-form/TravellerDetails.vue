<template>
  <div class="traveller-details">
    <p class="title">
      {{ __('Traveller details') }}
    </p>

    <div class="box box-border">
      <travellers-tabs
        :travellers="tempTravellers"
        :completed="completed"
        :active-index="activeTravellerIndex"
        @change="activeTravellerIndex = $event"
      />

      <traveller-form
        v-if="tempTravellers[activeTravellerIndex]"
        :traveller="tempTravellers[activeTravellerIndex]"
        :completed="completed[tempTravellers[activeTravellerIndex].id]"
        :main="mainCustomer(tempTravellers[activeTravellerIndex])"
        :loading="loading"
        @update="updateTraveller(activeTravellerIndex)"
      />
    </div>
  </div>
</template>

<script>
import TravellersTabs from './TravellersTabs'
import TravellerForm from './TravellerForm'
import {mapGetters} from 'vuex'
import axios from 'axios'

export default {
  components: {
    TravellersTabs,
    TravellerForm
  },

  data () {
    return {
      loading: false,
      type: 'success',
      completed: {
        //
      },
      activeTravellerIndex: 0,
      tempTravellers: []
    }
  },

  computed: {
    ...mapGetters([
      'authUser',
      'travellers',
      'currentPlanId',
      'query'
    ])
  },

  created () {
    this.tempTravellers = [
      ...this.travellers
    ]

    this.tempTravellers.forEach(e => {
      this.completed = {
        ...this.completed,
        [e.id]: !!e.email
      }
    })
  },

  methods: {
    mainCustomer (traveller) {
      return traveller.email === this.authUser.email
    },

    setMainCustomer () {
      this.tempTravellers.forEach(e => {
        e.main_customer = false
      })

      this.tempTravellers[this.activeTravellerIndex].main_customer = true
    },

    checkMainCustomer () {
      return !!this.tempTravellers.find(e => {
        e.main_customer === true
      })
    },

    async updateTraveller (index) {
      this.loading = true

      if (!this.checkEmails()) {
        this.loading = false

        return false
      }

      await axios
        .post(`/api/cart/plans/${this.currentPlanId}/travellers/${this.tempTravellers[index].id}/store`, {
          main_customer: this.tempTravellers[index].main_customer,
          ...this.tempTravellers[index]
        })
        .then(r => {
          if (r.data.success) {
            this.$notification.show({
              type: 'success',
              text: 'Traveller has been updated successfully.'
            })

            this.tempTravellers.splice(index, 1, r.data.entity)

            this.$store.commit('setTravellers', [...this.tempTravellers])

            this.completed = {
              ...this.completed,
              [this.tempTravellers[index].id]: true
            }
          }
        })

      this.loading = false
    },

    checkEmails () {
      let found = false

      let emails = []
      this.tempTravellers.forEach(e => {
        if (emails.includes(e.email)) {
          this.$notification.show({
            type: 'warning',
            text: 'The e-mail belongs to another customer.'
          })

          found = true
        }

        emails.push(e.email)
      })

      if (found) {
        return false
      }

      return true
    }
  }
}
</script>
