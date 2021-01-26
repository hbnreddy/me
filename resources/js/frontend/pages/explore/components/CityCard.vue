<template>
  <div
    class="col-lg-3 col-md-4 col-sm-6 col-12"
  >
    <div
      :class="{ 'explored': city.added }"
      class="card city-card"
    >
      <a
        href="#"
        class="text-decoration-none image-hover"
        @click.prevent="cityView()"
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
          v-onload="cityImage"
          class="card-img-top"
          alt="..."
        >
      </a>

      <div class="card-body">
        <div class="header">
          <div>
            <h5 class="card-title">
              <a
                :href="'#/city/' + city.id"
                class="text-decoration-none city-name"
              >{{ city.name }}, {{ city.name_suffix }}
              </a>
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
                {{ query.currency.symbol }}{{ cheapestExperiencePrice }}
              </span>
            </div>

            <div v-else-if="cheapestExperiencePrice === false" class="prices d-flex justify-content-end">
              <loading-circle
                :loading="cheapestExperiencePrice === false"
                :small="true"
                :color="'#FD753B'"
              />
            </div>

            <div v-if="cheapestFlightLoading" class="prices d-flex justify-content-end">
              <loading-circle
                :loading="cheapestFlightLoading"
                :small="true"
                :color="'#FD753B'"
              />
            </div>

            <div v-if="cheapestFlight && cheapestFlight.price" class="prices">
              <i
                class="fa fa-plane text-primary mr-2" data-toggle="tooltip"
                title="Best Round Trip Travel Cost Per Person"
              />

              <span v-if="cheapestFlight.extra">
                <span v-if="cheapestFlight.price < 0">-</span>
                <span v-else>+</span>

                {{ query.currency.symbol }}{{ Math.abs(Math.round(cheapestFlight.price)) }}
              </span>

              <span v-else>
                {{ query.currency.symbol }}{{ Math.round(cheapestFlight.price) }}
              </span>
            </div>

            <div class="prices">
              <i
                class="fa fa-bed text-primary mr-2" data-toggle="tooltip"
                title="Average 3 star Hotel Stay Cost Per Person"
              />{{ query.currency.symbol }}
              <span>{{ Math.round(city.accommodation) }}</span>
            </div>
          </div>
        </div>

        <div class="card-text">
          <city-weather-info
            v-if="hasWeather"
            :weathers="Object.keys(city.weathers)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CityWeatherInfo from './CityWeatherInfo'

import axios from 'axios'
import {mapGetters} from 'vuex'

export default {
  components: {
    CityWeatherInfo
  },

  props: {
    city: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      cheapestFlightLoading: false,
      cheapestExperiencePrice: false,
      cheapestFlight: false,
      placesCount: false,
      experiencesCount: false
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'currentItinerary',
      'currentPlanId'
    ]),

    cityImage () {
      // return `/storage/${this.city.image_url}`
      return 'https://picsum.photos/seed/picsum/700/900'
    },

    hasWeather () {
      return Object.keys(this.city.weathers).length
    }
  },

  created () {
    this.fetchCounts()
    this.fetchCheapestExperiencePrice()
    this.fetchCheapestRoute()
  },

  methods: {
    async fetchCheapestExperiencePrice () {
      await axios
        .get(`/api/cities/${this.city.id}/cheapest-experience`)
        .then(r => {
          if (r.data.success) {
            this.cheapestExperiencePrice = r.data.value
          }
        })
    },

    async fetchCheapestRoute () {
      this.cheapestFlightLoading = true

      await axios
        .get(`/api/rides/cheapest-route`, {
          params: {
            plan_id: this.currentPlanId ? this.currentPlanId : null,
            from_city_code: this.query.origin_city.code,
            to_city_code: this.city.code,
            travellers: '1-0-0',
            start_date: this.query.date.start_date,
            end_date: this.query.date.end_date,
            date_type: this.query.date.date_type ? this.query.date.date_type : 'none',
            price_only: 1
          }
        })
        .then(r => {
          if (r.data.success) {
            this.cheapestFlight = r.data.entity
          }

          this.cheapestFlightLoading = false
        })
    },

    async fetchCounts () {
      await axios
        .get(`/api/cities/${this.city.id}/counts`, {
          params: {
            themes: this.query.themes.join('-')
          }
        })
        .then(r => {
          this.placesCount = r.data.places_count
          this.experiencesCount = r.data.experiences_count
        })
    },

    cityView () {
      this.$router.push(`/city/${this.city.id}`)
    }
  }
}
</script>
