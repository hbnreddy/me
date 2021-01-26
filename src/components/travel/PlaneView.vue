<template>
  <div class="plane-view">
    <div class="box filter-wrap">
      <travel-filters v-model="filters" @filter="onFilter()" />

      <div class="position-relative travellers-picker-block">
        <input
          :value="travellersInputText"
          type="text"
          class="form-control travellers-input"
          placeholder="Travellers"
          @click.prevent="travellersPicker = true"
        >

        <travellers-picker-extended
          v-if="travellersPicker"
          v-on-clickaway="hideTravellersPicker"
          :travellers="queryTravellers"
          @change="onTravellersInput($event)"
          @hide="hideTravellersPicker()"
        />
      </div>
    </div>

    <!-- Trips -->
    <div class="trips-wrap">
      <rides-group
        v-if="currentPlanGroup.length"
        :group="currentPlanGroup"
        :travellers="currentPlanGroup[0].travellers"
        :modify="true"
      />

      <hr v-if="currentPlanGroup.length">

      <div v-if="groups.length || loading" class="body">
        <rides-group
          v-for="(group, index) in groups"
          :key="index"
          :group="group"
          :travellers="activeTravellersIds"
        />

        <observer v-if="offset !== 0" @intersect="fetchFlightGroups()" />

        <loading-circle
          :loading="loading"
          :small="true"
          :color="'#FD753B'"
          class="my-3"
        />
      </div>

      <div v-else-if="!loading" class="body">
        Sorry, no flights available at the moment..
      </div>
    </div>
  </div>
</template>

<script>
import TravellersPickerExtended from '../travellers/TravellersPickerExtended'
import LoadingCircle from '../loading/LoadingCircle'
import RidesGroup from './RidesGroup'

import Observer from '../../views/explore/components/Observer'
import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import TravelFilters from './TravelFilters'

export default {
  components: {
    TravelFilters,
    Observer,
    TravellersPickerExtended,
    RidesGroup,
    LoadingCircle
  },

  mixins: [
    clickaway
  ],

  props: {
    type: {
      type: String,
      default: () => 'round'
    }
  },

  data () {
    return {
      noMoreItems: false,
      queryTravellers: [],
      limit: 4,
      offset: 0,
      travellersPicker: false,
      groups: [],
      loading: false,
      selectedPriceItem: 0,
      chosenFlightId: false,
      roundTrips: [],
      filters: {
        max_changes: false,
        time: 0,
        max_price: 0,
        max_bags: 0,
        airlines: [],
        classes: ['f', 'ewoutr', 'ewr', 'ep', 'b']
      }
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'currentPlan',
      'itinerary'
    ]),

    currentPlanGroup () {
      return this.currentPlan.items.filter(e => e.type === 'travel')
    },

    travellersInputText () {
      return this.countTravellers
        ? 'Travellers ' + this.countTravellers.toString()
        : 'No travellers'
    },

    countTravellers () {
      return this.queryTravellers.filter(e => {
        return e.active
      }).length
    },

    duration () {
      return 0
    },

    hours () {
      return parseInt(this.duration / 60)
    },

    minutes () {
      return this.duration - (60 * this.hours)
    },

    totalPrice () {
      return 0
    },

    activeTravellersIds () {
      return this.queryTravellers
        .filter(e => e.active)
        .map(e => e.id)
    }
  },

  created () {
    this.queryTravellers = [
      ...this.currentPlan.travellers.map(e => {
        return {
          id: e.id,
          reference_key: e.reference_key,
          active: true,
          type: e.type
        }
      })
    ]

    this.fetchFlightGroups()
  },

  methods: {
    onTravellersInput (values) {
      this.queryTravellers = [
        ...values
      ]

      this.offset = 0
      this.groups = []
      this.noMoreItems = false

      this.fetchFlightGroups()
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    onFilter () {
      this.offset = 0
      this.groups = []
      this.noMoreItems = false

      // this.fetchFlightGroups()
    },

    async fetchFlightGroups () {
      if (this.noMoreItems) {
        return false
      }

      this.loading = true

      let filters = {
        //
      }

      Object.keys(this.filters)
        .forEach(key => {
          if (this.filters[key]) {
            if (['max_changes', 'max_bags', 'max_price'].includes(key)) {
              filters = {
                ...filters,
                [key]: this.filters[key]
              }
            } else if (key === 'airlines') {
              //
            } else if (key === 'time') {
              filters = {
                ...filters,
                departure: this.filters[key].departure.join(','),
                arrival: this.filters[key].arrival.join(',')
              }
            } else if (key === 'classes' && this.filters[key].length) {
              filters = {
                ...filters,
                classes: this.filters[key].join(',')
              }
            }
          } else if (key === 'max_changes' && this.filters[key] === 0) {
            filters = {
              ...filters,
              max_changes: 0
            }
          }
        })

      const travellers = [
        this.queryTravellers.filter(e => {
          return e.active && e.type === 'adults'
        }).length,
        this.queryTravellers.filter(e => {
          return e.active && e.type === 'children'
        }).length,
        this.queryTravellers.filter(e => {
          return e.active && e.type === 'infants'
        }).length
      ]

      let routes = []
      for (let i = 0; i < this.itinerary.length - 1; ++i) {
        routes.push({
          departure_city_code: this.itinerary[i].city_code,
          arrival_city_code: this.itinerary[i + 1].city_code,
          date: this.itinerary[i + 1].date,
          travellers: travellers.join(',')
        })
      }

      const requestData = {
        routes,
        start_date: this.currentPlan.start_date,
        end_date: this.currentPlan.end_date,
        type: 'plane',
        offset: this.offset,
        limit: this.limit
      }

      const entities = await this.$store.dispatch('fetchFlightGroups', requestData)

      this.groups = [
        ...this.groups,
        ...entities
      ]

      const count = entities.length
      this.offset += count

      if (count !== this.limit) {
        this.noMoreItems = true
      }

      this.loading = false
    },

    chooseFlight (id) {
      this.chosenFlightId = id
    },

    onSelectPriceItem (index) {
      this.selectedPriceItem = index
    }
  }
}
</script>
