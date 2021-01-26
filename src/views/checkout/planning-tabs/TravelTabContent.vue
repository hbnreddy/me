<template>
  <div class="travel-tab-content">
    <travel-itinerary :itinerary="itinerary" />

    <component
      :is="`${travelType}-view`"
      :type="selectedRouteType"
    />
  </div>
</template>

<script>
import PlaneView from '../../../components/travel/PlaneView'
import TrainView from '../../../components/travel/TrainView'
import TravelItinerary from '../../../components/travel/TravelItinerary'

import { mapGetters } from 'vuex'

export default {
  components: {
    TravelItinerary,
    PlaneView,
    TrainView
  },

  data () {
    return {
      travelType: 'plane',
      routeSlug: '',
      cityCodeFrom: '',
      cityCodeTo: '',
      navigationTabs: 1,
      selectedRoutes: [],
      selectedRoutesId: [],
      selectedRouteType: this.$const('ROUTE_TYPE_ONEWAY')
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'currentPlan',
      'itinerary'
    ])
  }
}
</script>
