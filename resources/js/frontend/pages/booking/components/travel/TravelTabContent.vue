<template>
  <div>
    <div class="box box-border">
      <div class="itinerary travel">
        <div
          v-for="(route, index) in itinerary"
          :key="index"
          :class="{ active: isActive(index) }"
          class="route"
        >
          <div class="dot">
            <div class="city-code">
              {{ route.code }}
            </div>
            <i class="fa fa-circle icon" />
          </div>

          <div
            v-if="index !== itinerary.length - 1"
            class="route-line"
            @click="selectRoute(index, itinerary.length)"
          />
        </div>
      </div>

      <div class="head">
        <div class="switch-tabs">
          <div
            :class="{active: navigationTabs === 1}"
            class="tab"
            @click="switchNavigationTabs(1)"
          >
            <i class="fa fa-plane" />
          </div>

          <div
            :class="{active: navigationTabs === 2}"
            class="tab"
            @click="switchNavigationTabs(2)"
          >
            <i class="fa fa-train" />
          </div>
        </div>

        <div>
          <route-info
            v-for="(selectedRoute, key) in selectedRoutes"
            :key="key"
            :routes="selectedRoute"
            class="d-flex align-items-center mb-3"
          />
        </div>
      </div>
    </div>

    <plane-view
      v-if="navigationTabs === 1"
      :route-type="selectedRouteType"
    />

    <train-view
      v-if="navigationTabs === 2"
      :route-type="selectedRouteType"
    />
  </div>
</template>

<script>
import RouteInfo from '../RouteInfo'
import PlaneView from './PlaneView'
import TrainView from './TrainView'

export default {
  components: {
    PlaneView,
    TrainView,
    RouteInfo
  },

  data () {
    return {
      navigationTabs: 1,
      selectedRoutes: [],
      selectedRoutesId: [],
      selectedRouteType: this.$const('ROUTE_TYPE_ROUND'),
      itinerary: [
        {
          city: 'Hyderabad',
          code: 'HYD'
        },
        {
          city: 'Paris',
          code: 'PAR'
        },
        {
          city: 'Zurich',
          code: 'ZUR'
        },
        {
          city: 'London',
          code: 'LON'
        },
        {
          city: 'Hyderabad',
          code: 'HYD'
        }
      ]
    }
  },

  created () {
    if (this.selectedRoutes) {

      const length = this.itinerary.length
      this.selectedRoutesId = [0, length - 2]

      this.selectedRoutes = [
        [
          this.itinerary[0],
          this.itinerary[1]
        ],
        [
          this.itinerary[length - 2],
          this.itinerary[length - 1]
        ]
      ]
    }
  },

  methods: {
    switchNavigationTabs (index) {
      this.navigationTabs = index
    },

    isActive (index) {
      return this.selectedRoutesId.includes(index)
    },

    selectRoute (index, length) {
      if (index === 0 || index === length - 2) {
        this.selectedRouteType = this.$const('ROUTE_TYPE_ROUND')

        this.selectedRoutesId = []
        this.selectedRoutesId.push(0, length - 2)

        this.selectedRoutes = [
          [
            this.itinerary[0],
            this.itinerary[1]
          ],
          [
            this.itinerary[length - 2],
            this.itinerary[length - 1]
          ]
        ]

      } else {
        this.selectedRouteType = this.$const('ROUTE_TYPE_ONEWAY')

        this.selectedRoutesId = []
        this.selectedRoutesId.push(index)

        this.selectedRoutes = [
          [
            this.itinerary[index],
            this.itinerary[index + 1]
          ]
        ]
      }
    }
  }
}
</script>
