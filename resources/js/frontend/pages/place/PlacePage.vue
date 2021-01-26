<template>
  <div class="application">
    <navbar />

    <place-page-header
      v-if="city"
      :city="city"
      :filters="activeFilters"
      :enable-filter="entity.type === 'activity'"
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
          <span v-if="filteredActivities.length">
            {{ __('Experiences') }} ({{ filteredActivities.length }})
          </span>

          <span v-else>
            {{ __('No experiences') }}
          </span>
        </div>

        <div class="row">
          <div class="col-lg-3 col-md-4">
            <sidebar
              :items="filteredActivities"
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

    <footer-component />
  </div>
</template>

<script>
import Navbar from '../../components/Navbar'
import PlaceComponent from './components/PlaceComponent'
import PlacePageHeader from './PlacePageHeader'
import FooterComponent from '../../components/Footer'
import Sidebar from './components/Sidebar'
import LoadingCircle from '../../../components/LoadingCircle'

import axios from 'axios'
import {mixin as clickaway} from 'vue-clickaway'
import { STANDARD_PLACE, ACTIVITY_PLACE } from './place-types'
import { route } from '../../mixins/route'
import { mapGetters } from 'vuex'
import * as moment from 'moment'

export default {
  components: {
    LoadingCircle,
    PlaceComponent,
    Sidebar,
    PlacePageHeader,
    Navbar,
    FooterComponent
  },

  mixins: [
    clickaway,
    route
  ],

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
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
        duration: false,
        refundable: false,
        pass_type: false,
        meals_included: false
      }
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ]),

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
            currency: this.data.query.currency.symbol
          },
          rating: e.reviews_avg,
          reviews_number: e.reviews_number,
          media: e.media
        }
      })
    }
  },

  created () {
    if (this.data.user) {
      this.$store.commit('setAuthUser', {
        ...this.data.user
      })
    }

    this.$store.commit('setNotifications', this.data.notifications)
    this.$store.commit('setCurrentPlanId', this.data.current_plan_id)
    this.$store.commit('setQuery', this.data.query)

    this.searchStartDate = this.query.date.start_date
    this.searchEndDate = this.query.date.end_date

    this.processPlace()
  },

  methods: {
    // async fetchCheapestRoute () {
    //   let citiesCodes = [this.data.place.city.code]
    //
    //   await axios
    //     .get(`/api/rides/cheapest-route`, {
    //       params: {
    //         from_city_code: this.query.origin_city.code,
    //         to_cities_codes: citiesCodes.join(','),
    //         travellers: Object.values(this.query.travellers).join('-'),
    //         start_date: this.query.date.start_date,
    //         end_date: this.query.date.end_date
    //       }
    //     })
    //     .then(r => {
    //       if (r.data.success) {
    //         this.cheapestRoute = r.data.entity
    //         this.$store.commit('setCheapestFlight', r.data.entity)
    //         this.searchStartDate = moment(this.cheapestRoute.departure_date).format('YYYY-MM-DD')
    //         this.searchEndDate = moment(this.cheapestRoute.return_date).format('YYYY-MM-DD')
    //       }
    //     })
    // },

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

      const place = this.data.place

      this.entity = {
        id: activity.id,
        type: ACTIVITY_PLACE,
        name: activity.name,
        name_suffix: place.name_suffix,
        description: activity.about,
        rating: activity.rating,
        reviews_number: activity.reviews_number,
        media: activity.media,
        price: activity.price,
        identifiers: {
          activity_uuid: activity.id,
          place_id: place.id,
          city_id: place.city_id,
          city_code: place.city.code,
          marker_id: place.marker_id,
          venue_id: place.venue_id
        }
      }
    },

    onDone () {
      this.$redirect('explore', this.routeParams, `/city/${this.city.id}`)
    },

    async processPlace () {
      // if (!this.currentPlanId) {
      //   await this.fetchCheapestRoute()
      // }

      const place = this.data.place
      this.city = place.city

      if (!place.venue) {
        this.entity = {
          id: place.id,
          type: STANDARD_PLACE,
          name: place.name,
          name_suffix: place.name_suffix,
          description: place.description,
          media: place.media_resources,
          rating: place.rating,
          identifiers: {
            place_id: place.id,
            city_id: place.city_id,
            city_code: place.city.code,
            marker_id: place.marker_id,
            venue_id: place.venue_id,
            activity_uuid: false
          }
        }

        this.loading = false
        this.sidebarLoading = false
      } else {
        const dates = this.data.query.date

        await axios
          .get(`/api/venues/${place.venue.id}/activities`, {
            params: {
              start_date: this.searchStartDate,
              end_date: this.searchEndDate,
              offset: 0,
              limit: 1,
              first_only: 1
            }
          })
          .then(r => {
            if (r.data.success) {
              const result = r.data.entities[0]

              this.entity = {
                id: result.uuid,
                price: {
                  value: result.retail_price.value,
                  currency: this.data.query.currency.symbol
                },
                type: ACTIVITY_PLACE,
                name: result.title,
                name_suffix: place.name_suffix,
                description: result.about,
                rating: result.reviews_avg,
                reviews_number: result.reviews_number,
                identifiers: {
                  activity_uuid: result.uuid,
                  place_id: place.id,
                  city_id: place.city_id,
                  city_code: place.city.code,
                  marker_id: place.marker_id,
                  venue_id: place.venue.id
                }
              }

              this.activities.push(result)
            }
          })

        this.loading = false

        if (this.entity.id) {
          this.fetchActivities()
        }
      }
    },

    async fetchActivities (loadMore = false) {
      const dates = this.data.query.date

      this.canLoadMore = false

      await axios
        .get(`/api/venues/${this.entity.identifiers.venue_id}/activities`, {
          params: {
            start_date: this.searchStartDate,
            end_date: this.searchEndDate,
            offset: this.offset,
            limit: this.limit
          }
        })
        .then(r => {
          if (r.data.success) {
            if (loadMore) {
              this.activities = [
                ...this.activities,
                ...r.data.entities
              ]
            } else {
              this.activities = r.data.entities
            }

            const count = r.data.entities.length
            this.offset += count

            this.canLoadMore = count === this.limit
          }

          this.sidebarLoading = false
        })
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
      this.activeFilters = {
        ...filter
      }
    }
  }
}
</script>
