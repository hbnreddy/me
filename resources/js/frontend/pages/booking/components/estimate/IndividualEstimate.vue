<template>
  <div
    id="nav-people"
    class="tab-pane fade show"
    role="tabpanel"
    aria-labelledby="nav-activities-tab"
  >
    <div class="tab-header">
      <div class="tab-title">
        {{ __('Individual Estimate') }}
      </div>

      <div class="more-options">
        <i class="fa fa-list-ul" />
      </div>
    </div>

    <ul class="travellers-overview">
      <li
        v-for="(traveller, index) in travellers"
        :key="index"
        class="traveller-item"
      >
        <div class="left-side">
          <i class="fa fa-user" />

          <div class="name">
            {{ printName(traveller) }}
          </div>
        </div>

        <div class="right-side">
          {{ query.currency.symbol }} {{ travellerPayment(traveller.reference_key) }}
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
  props: {
    travellers: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      estimates: {
        //
      }
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'currentPlanId'
    ])
  },

  created () {
    if (!Object.keys(this.estimates).length) {
      this.fetchIndividualEstimate()
    }
  },

  methods: {
    travellerPayment (key) {
      return this.estimates[key]
        ? this.estimates[key].total_payment
        : 0
    },

    async fetchIndividualEstimate () {
      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/individual-estimate`)
        .then(r => {
          if (r.data.success) {
            this.estimates = r.data.entities
          }
        })
    },

    printName (traveller) {
      const attributes = traveller

      let fullName = attributes.reference_key

      if (attributes.first_name) {
        fullName = attributes.first_name

        if (attributes.last_name) {
          fullName = `${fullName} ${attributes.last_name}`
        }
      }

      return fullName
    }
  }
}
</script>
