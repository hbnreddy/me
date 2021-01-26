<template>
  <div
    class="col-lg-3 col-md-4 col-sm-6 col-12"
  >
    <router-link
      :to="cityRoute"
      class="card city-card text-decoration-none"
      :class="{'blob grey' : index < 4 && pulsating }"
    >
      <div
        class="text-decoration-none image-hover"
      >
        <div
          class="centered-text"
          style="text-align: center;"
        >
          {{ __('Explore') }} {{ city.name }}
        </div>

        <div v-if="1 === 2" class="weather-card">
          <i class="fa fa-cloud" />
          24 °C
        </div>

        <img
          :src="cityImage"
          class="card-img-top"
          alt="..."
        >
      </div>

      <div class="card-body">
        <div class="header">
          <div>
            <h5 class="card-title">
              <span class="text-decoration-none city-name">
                {{ city.name }}, {{ city.name_suffix }}
              </span>
            </h5>

            <div class="subtitle">
              <span v-if="placesCount !== false">
                {{ placesCount }} {{ __('Places of Interest') }}
              </span>

              <span v-else>
                {{ __('Loading') }}..
              </span>
            </div>

            <div
              v-if="experiencesCount !== false"
              class="subtitle"
            >
              <span v-if="experiencesCount !== false">
                {{ experiencesCount }} Experiences
              </span>

              <span v-else>
                {{ __('Loading') }}..
              </span>
            </div>
          </div>

          <div v-if="!city.added">
            <div v-if="cheapestExperiencePrice" class="prices">
              <i
                class="fa fa-fire text-primary mr-2" data-toggle="tooltip"
                title="Average Ticket Cost Per Entrance Per Person"
              />

              <span>
                {{ query.currency }}{{ Math.round(cheapestExperiencePrice) }}
              </span>
            </div>

            <div v-if="cheapestExperienceLoading" class="prices d-flex justify-content-end">
              <loading-circle
                :loading="true"
                :small="true"
                :color="'#FD753B'"
              />
            </div>

            <div v-if="cheapestFlightLoading" class="prices d-flex justify-content-end">
              <loading-circle
                :loading="cheapestFlightLoading === true"
                :small="true"
                :color="'#FD753B'"
              />
            </div>

            <div v-if="cheapestFlight && cheapestFlight.amount" class="prices">
              <i
                class="fa fa-plane text-primary mr-2" data-toggle="tooltip"
                title="Best Round Trip Travel Cost Per Person"
              />

              <span v-if="cheapestFlight.extra">
                <span v-if="cheapestFlight.amount < 0">-</span>
                <span v-else>+</span>

                {{ cheapestFlight.currency }}&nbsp;{{ Math.abs(Math.round(cheapestFlight.amount)) }}
              </span>

              <span v-else>
                {{ cheapestFlight.currency }}&nbsp;{{ Math.round(cheapestFlight.amount) }}
              </span>
            </div>

            <div v-if="cheapestHotelLoading" class="prices d-flex justify-content-end">
              <loading-circle
                :loading="cheapestHotelLoading === true"
                :small="true"
                :color="'#FD753B'"
              />
            </div>

            <div v-if="cheapestHotel" class="prices">
              <i
                class="fa fa-bed text-primary mr-2" data-toggle="tooltip"
                title=""
              />
              <span>
                {{ query.currency }}{{ Math.abs(Math.round(cheapestHotel.price)) }}
              </span>
            </div>
          </div>
        </div>

        <div v-if="city.weathers" class="card-text">
          <city-weather-info
            v-if="hasWeather"
            :weathers="Object.keys(city.weathers)"
          />
        </div>
      </div>

      <div
        v-if="isAddedToPlan(city)"
        class="added-block"
      >
        <i class="fa fa-check-circle-o added-icon" />

        <span class="text">Added</span>
      </div>
    </router-link>
  </div>
</template>

<script>
import CityWeatherInfo from './CityWeatherInfo'

import moment from 'moment'
import {mapGetters} from 'vuex'
import { route } from '../../../mixins/route'

export default {
  components: {
    CityWeatherInfo
  },

  mixins: [
    route
  ],

  props: {
    index: {
      type: Number,
      required: true
    },

    city: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      pulsating: false,
      pollingTime: 30,
      cheapestExperienceLoading: false,
      cheapestHotel: false,
      cheapestHotelLoading: false,
      cheapestFlightLoading: false,
      cheapestExperiencePrice: false,
      cheapestFlight: false,
      placesCount: 0,
      experiencesCount: false
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId',
      'query',
      'itinerary',
      'originCity'
    ]),

    cityRoute () {
      return {
        name: 'city',
        params: {
          id: this.city.id,
          index: this.index,
          planId: this.currentPlanId
        },
        query: this.routeQuery
      }
    },

    cityImage () {
     return  require('../../../assets/landing-page-image.jpg')
      // return '../../../assets/landing-page-image.jpg'
    },

    hasWeather () {
      return Object.keys(this.city.weathers).length
    }
  },

  mounted () {
    setTimeout(() => {
      this.pulsating = true
    }, 1200)
  },

  created () {
    this.fetchCounts()
    // this.fetchCheapestExperiencePrice()
    this.fetchCheapestRoute()
    // this.fetchCheapestHotel()
  },

  methods: {
    async fetchCheapestHotel () {
      this.cheapestHotelLoading = true

      const date = this.query.start_date

      const startDate = moment(this.query.start_date)
      const endDate = moment(this.query.end_date)
      const duration = endDate.diff(startDate, 'days') - 1

      this.cheapestHotel = this.$store.dispatch('fetchCheapestHotel', {
        city_code: this.city.code,
        rooms: this.processHotelRooms().join(','),
        duration,
        date
      })

      if (this.cheapestHotel.polling && this.pollingTime) {
        this.pollingTime -= 3
        setTimeout(this.fetchCheapestHotel, 3000)
      }

      if (this.cheapestHotel.price || this.pollingTime <= 0) {
        this.cheapestHotelLoading = false
      }
    },

    processHotelRooms () {
      let rooms = []

      let adults = this.query.travellers.adults
      let children = this.query.travellers.children
      let infants = this.query.travellers.infants

      let total = adults + children + infants

      if (total <= 4) {
        rooms.push(`${adults}.${children}.${infants}`)
      } else {
        while (adults || children || infants) {
          let room = ''
          total--

          if (adults > 2 && (children > 2 || infants > 2)) {
            room = `1`
            adults--
          } else if (adults > 2) {
            room = `2`
            adults -= 2
          } else {
            room = `${adults}`
            adults = 0
          }

          if (children > 2) {
            room = `${room}.2`
            children -= 2
          } else {
            room = `${room}.${children}`
            children = 0
          }

          if (infants > 2) {
            room = `${room}.2`
            infants -= 2
          } else {
            room = `${room}.${infants}`
            infants = 0
          }

          rooms.push(room)
        }
      }

      return rooms
    },

    async fetchCheapestRoute () {
      // console.log(Object.values(this.query.travellers).join(','));
      
console.log(this.query);
      this.cheapestFlightLoading = true
      this.cheapestFlight = await this.$store.dispatch('fetchCheapestRoute', {
        departure_city_code: this.originCity.code,
        arrival_city_code: this.city.code,
        departure_date: this.query.start_date,
        return_date: this.query.end_date,
        travellers: Object.values(this.query.travellers).join(','),
        round_trip: 1,
        price_only: 1
      })

      this.cheapestFlightLoading = false
    },

    async fetchCheapestExperiencePrice () {
      this.cheapestExperienceLoading = true
      this.cheapestExperiencePrice = await this.$store.dispatch('fetchCheapestExperience', this.city.id)
      this.cheapestExperienceLoading = false
    },

    async fetchCounts () {
      const counts = await this.$store.dispatch('fetchCityCounts', {
        id: this.city.id,
        themes: this.query.themes.join(',')
      })

      this.placesCount = counts.places_count
      this.experiencesCount = counts.experiences_count
    },

    isAddedToPlan (city) {
      return this.itinerary
        .map(item => item.city_code)
        .includes(city.code)
    }
  }
}
</script>
