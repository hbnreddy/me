<template>
  <div>
    <div class="left">
      <div class="start">
        <i class="fa fa-circle-o icon" />
        {{ routes[0].city }}
      </div>

      <div class="transition">
        <i class="fa fa-long-arrow-right mx-3" />
      </div>

      <div class="end">
        <i class="fa fa-map-marker icon" />
        {{ routes[1].city }}
      </div>
    </div>

    <div class="right">
      <div class="date">
        <i class="fa fa-calendar icon mr-3" />
        {{ moment().format('DD MMM YYYY') }}
      </div>

      <div class="position-relative">
        <input
          :value="travellersInputText"
          type="text"
          class="form-control"
          placeholder="Travellers"
          @click.prevent="showTravellersPicker()"
        >

        <travellers-picker-extended
          v-if="travellersPicker"
          v-on-clickaway="onClickOutsideTravellersPicker"
          :value="actualQuery.travellers"
          @input="onTravellersInput($event)"
          @hide="hideTravellersPicker()"
        />
      </div>
    </div>
  </div>
</template>

<script>
import TravellersPickerExtended from '../../../components/TravellersPickerExtended'
import {mixin as clickaway} from 'vue-clickaway'
import {cloneDeep} from 'lodash'
import {mapGetters} from 'vuex'

export default {
  components: {
    TravellersPickerExtended
  },

  mixins: [
    clickaway
  ],

  props: {
    routes: {
      type: Object | Array,
      default: () => {
      }
    }
  },

  data () {
    return {
      navigationTabs: 1,
      travellersPicker: false,
      actualQuery: {
      }
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ]),

    travellersInputText () {
      return this.countTravellers() ? 'Travellers ' + this.countTravellers().toString() : ''
    }
  },

  created () {
    this.actualQuery = cloneDeep(this.query)
  },

  methods: {
    switchNavigationTabs (index) {
      this.navigationTabs = index
    },

    onClickOutsideTravellersPicker () {
      this.travellersPicker = false
    },

    onTravellersInput (value) {
      this.actualQuery.travellers = {
        ...value
      }

      this.updateQuery()
    },

    updateQuery () {
      this.$store.commit('setQuery', cloneDeep(this.actualQuery))
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    showTravellersPicker () {
      this.travellersPicker = true
    },

    countTravellers () {
      return this.actualQuery.travellers.adults
          + this.actualQuery.travellers.children
          + this.actualQuery.travellers.infants
    }
  }
}
</script>
