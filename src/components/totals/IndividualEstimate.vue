<template>
  <div
    class="tab-pane fade show active"
  >
    <div class="tab-header">
      <div class="tab-title">
        {{ __('Individual Estimate') }}
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
          {{ currency }} {{ traveller.total_amount }}
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
      required: true
    },

    currency: {
      type: String,
      required: true
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
      'currentPlan'
    ])
  },

  methods: {
    async fetchIndividualEstimate () {
      await axios
        .get(`/cart/plans/${this.currentPlanId}/individual-estimate`)
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
