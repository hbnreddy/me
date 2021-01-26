<template>
  <div class="poi-content">
    <loading-circle
      :loading="loading"
      text="Loading place"
    />

    <place-content
      v-if="!loading"
      :entity="entity"
      :is-activity="isActivity"
    >
      <reviews
        v-if="isActivity && reviews"
        slot="below-stories"
        :reviews="reviews"
        :average-rating="entity.rating"
      />

      <things-to-do
        v-if="venues.length"
        slot="below-details"
        :items="venues"
      />

      <place-policy
        slot="above-stories"
        :inclusions="taxonomies.inclusions"
        :refund-policies="refundPolicies"
      />
    </place-content>
  </div>
</template>

<script>
import PlaceContent from './PlaceContent'
import LoadingCircle from '../../../../components/LoadingCircle'
import ThingsToDo from './ThingsToDo'
import Reviews from './Reviews'
import PlacePolicy from './PlacePolicy'

import axios from 'axios'

export default {
  components: {
    LoadingCircle,
    PlacePolicy,
    ThingsToDo,
    PlaceContent,
    Reviews
  },

  props: {
    entity: {
      type: Object,
      required: true
    },

    isActivity: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      loading: false,
      taxonomies: {
        inclusions: [],
        exclusions: []
      },
      refundPolicies: [],
      venues: [],
      reviews: []
    }
  },

  watch: {
    entity: {
      handler () {
        if (this.isActivity) {
          this.loading = true

          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          })

          this.fetchActivityData()
        }
      },
      immediate: true
    }
  },

  methods: {
    async fetchActivityData () {
      await this.fetchVenues()
      await this.fetchReviews()

      this.fetchTaxonomies()
      this.fetchRefundPolicies()

      this.loading = false
    },

    async fetchVenues () {
      await axios
        .get(`/api/activities/${this.entity.id}/venues`)
        .then(r => {
          if (r.data.success) {
            this.venues = r.data.entities
          }
        })
    },

    async fetchReviews () {
      await axios
        .get(`/api/activities/${this.entity.id}/comments`, {
          params: {
            offset: 0,
            limit: 6
          }
        })
        .then(r => {
          if (r.data.success) {
            this.reviews = r.data.entities
          }
        })
    },

    async fetchTaxonomies () {
      await axios
        .get(`/api/activities/${this.entity.id}/taxonomies`)
        .then(r => {
          if (r.data.success) {
            this.taxonomies = {
              inclusions: r.data.entities.filter(e => {
                return e.type === 'INCLUSION'
              }),
              exclusions: r.data.entities.filter(e => {
                return e.type === 'EXCLUSION'
              })
            }
          }
        })
    },

    async fetchRefundPolicies () {
      await axios
        .get(`/api/activities/${this.entity.id}/refund-policies`)
        .then(r => {
          if (r.data.success) {
            this.processRefundPolicies(r.data.entities)
          }
        })
    },

    processRefundPolicies (policies) {
      this.refundPolicies = policies
        .map(e => {
          return {
            value: e.value,
            symbol: e.type === 'PERCENTAGE' ? '%' : e.currency_code,
            period: this.processPeriodString(e.period)
          }
        })
    },

    processPeriodString (period) {
      period = period.substr(1, period.length - 1).toLowerCase()

      let i = 0
      let time = {
        w: 0,
        d: 0,
        h: 0,
        m: 0
      }

      let value = 0
      while (i < period.length) {
        if (period[i] !== 't') {
          if (period[i] === 'w') {
            time['w'] = value
            value = 0
          } else if (period[i] === 'd') {
            time['d'] = value
            value = 0
          } else if (period[i] === 'h') {
            time['h'] = value
            value = 0
          } else if (period[i] === 'm') {
            time['m'] = value
            value = 0
          } else {
            value += parseInt(period[i])
          }
        }

        i++
      }

      const keyMap = {
        w: 'week',
        d: 'day',
        h: 'hour',
        m: 'minute'
      }

      let text = ''
      Object.keys(time).forEach(key => {
        if (time[key]) {
          text += `${time[key]} ${keyMap[key]}(s) `
        }
      })

      return text
    }
  }
}
</script>
