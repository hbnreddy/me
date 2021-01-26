<template>
  <div class="place-page">
    <place-page-header
      v-if="city"
      :city="city"
      :filters="activeFilters"
      :price-range="priceRange"
      :enable-filter="entity.type === 'activity'"
      @price-changed="onPriceChange($event)"
      @filter="filterActivities($event)"
    />

    <loading-circle
      :loading="loading"
      text="Loading place"
    />

    <div v-if="entity" class="container-fluid py-4">
      <div class="poi">
        <div
          class="number-of-experinces font-weight-bold mb-3"
        >
          <span v-if="counts">
            {{ __('Experiences') }} ({{ counts }})
          </span>

          <span v-else-if="isActivity">
            {{ __('No experiences') }}
          </span>
        </div>

        <div class="row">
          <div class="col-lg-3 col-md-4">
            <sidebar
              :items="filteredActivities"
              :filters="activeFilters"
              :loading="sidebarLoading"
              @selected="onSelectActivity($event)"
              @loadMore="loadMoreActivities()"
            />
          </div>

          <div class="col-lg-8 offset-lg-1 col-md-8">
            <place-component
              :entity="entity"
              :is-activity="isActivity"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PlaceComponent from './components/PlaceComponent'
import PlacePageHeader from './PlacePageHeader'
import Sidebar from './components/Sidebar'
import LoadingCircle from '../../components/loading/LoadingCircle'

import {mixin as clickaway} from 'vue-clickaway'
import { STANDARD_PLACE, ACTIVITY_PLACE } from './place-types'
import { route } from '../../mixins/route'
import { mapGetters } from 'vuex'

export default {
  components: {
    LoadingCircle,
    PlaceComponent,
    Sidebar,
    PlacePageHeader
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      hasActiveFilters: false,
      activitiesCount: 0,
      priceRange: {
        min: 0,
        max: 0
      },
      cheapestRoute: false,
      canLoadMore: false,
      activities: [],
      offset: 0,
      limit: 4,
      sidebarLoading: true,
      loading: true,
      entity: false,
      city: null,
      hasExperiences: false,
      currentActivity: false,
      place: false,
      activeFilters: {
        pickup: false,
        price: 0,
        duration: false,
        refundable: false,
        pass_type: false,
        meals_included: false
      }
    }
  },

  computed: {
    ...mapGetters([
      'user',
      'query'
    ]),

    counts () {
      if (this.hasActiveFilters) {
        return this.filteredActivities.length
      }

      return this.activitiesCount
    },

    isActivity () {
      return this.entity.type === ACTIVITY_PLACE
    },

    filteredActivities () {
      if (this.entity.type === STANDARD_PLACE) {
        return [
          {
            name: this.entity.name,
            type: STANDARD_PLACE,
            rating: this.entity.rating,
            media: this.entity.media
          }
        ]
      }

      return this.activities.map(e => {
        return {
          id: e.uuid,
          name: e.title,
          type: ACTIVITY_PLACE,
          about: e.about,
          price: {
            value: e.retail_price.value,
            currency: this.data.query.currency
          },
          rating: e.reviews_avg,
          reviews_number: e.reviews_number,
          media: e.media,
          pickup: e.pickup
        }
      })
        .filter(e => {
          return e.price.value <= this.activeFilters.price &&
            e.pickup === this.activeFilters.pickup
        })
    }
  },

  created () {
    this.searchStartDate = this.query.start_date
    this.searchEndDate = this.query.end_date

    this.processPlace()
  },

  methods: {
    async loadFirstActivity () {
      this.entity = this.$store.dispatch('', {
        venueId: this.this.place.venue.id,
        start_date: this.searchStartDate,
        end_date: this.searchEndDate,
        offset: 0,
        limit: 1
      })
    },

    onPriceChange (value) {
      this.activeFilters.price = value
    },

    async fetchActivitiesDetails () {
      const details = await this.$store.dispatch('fetchActivitiesDetails', {
        venueId: this.data.this.place.venue.id,
        startDate: this.startDate,
        endDate: this.endDate
      })

      this.priceRange = {
        min: details.min,
        max: details.max
      }

      this.activeFilters.price = this.priceRange.max % 50 === 0
        ? this.priceRange.max
        : this.priceRange.max + (50 - this.priceRange.max % 50)

      this.activitiesCount = details.count
    },

    async loadMoreActivities () {
      if (this.isActivity && this.canLoadMore) {
        this.sidebarLoading = true

        await this.fetchActivities(true)

        this.sidebarLoading = false
      }
    },

    onSelectActivity (activity) {
      if (!this.isActivity) {
        return false
      }

      this.entity = {
        id: activity.id,
        type: ACTIVITY_PLACE,
        name: activity.name,
        name_suffix: this.place.name_suffix,
        description: activity.about,
        rating: activity.rating,
        reviews_number: activity.reviews_number,
        media: activity.media,
        price: activity.price,
        activity_uuid: activity.id,
        place_id: this.place.id,
        city_id: this.place.city_id,
        city_code: this.place.city.code,
        marker_id: this.place.marker_id,
        venue_id: this.place.venue_id
      }
    },

    async processPlace () {
      this.place = await this.$store.dispatch('fetchPlace', this.$route.params.placeId)

      this.city = this.place.city

      if (!this.place.venue) {
        this.entity = {
          id: this.place.id,
          type: STANDARD_PLACE,
          name: this.place.name,
          name_suffix: this.place.name_suffix,
          description: this.place.description,
          media: this.place.media_resources,
          rating: this.place.rating,
          place_id: this.place.id,
          city_id: this.place.city_id,
          city_code: this.place.city.code,
          marker_id: this.place.marker_id,
          venue_id: this.place.venue_id,
          activity_uuid: false
        }

        this.loading = false
        this.sidebarLoading = false
      } else {
        this.fetchActivitiesDetails()

        this.loadFirstActivity()

        this.loading = false

        if (this.entity.id) {
          this.fetchActivities()
        }
      }
    },

    async fetchActivities (loadMore = false) {
      this.canLoadMore = false

      const activities = this.$store.dispatch('', {
        start_date: this.searchStartDate,
        end_date: this.searchEndDate,
        offset: this.offset,
        limit: this.limit
      })

      if (loadMore) {
        this.activities = [
          ...this.activities,
          ...activities
        ]
      } else {
        this.activities = activities
      }

      const count = activities.length
      this.offset += count

      this.canLoadMore = count === this.limit
      this.sidebarLoading = false
    },

    processFilters () {
      let features = {
        meal_included: false,
        english_offer: false
      }

      this.currentActivity.languages.forEach(l => {
        if (l.code === 'en') {
          features.english_offer = true
        }
      })

      if (this.currentActivity.food.length) {
        features.meal_included = true
      }

      this.currentActivity.features.forEach(e => {
        features[e.code] = true
      })
    },

    filterActivities (filter) {
      this.hasActiveFilters = true

      this.activeFilters = {
        ...filter
      }
    }
  }
}
</script>
