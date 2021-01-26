<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-4">
        Places of Interest
      </h3>
    </div>

    <div class="d-flex justify-content-between align-items-center my-2">
      <div class="d-flex align-items-center">
        <div class="d-flex justify-content-center">
          <select
            v-model="query.country_id"
            class="form-control mr-3"
            @change="onCountryChange()"
          >
            <option
              value="0"
              selected
            >
              None
            </option>

            <option
              v-for="country in countries.enabled"
              :key="country.id"
              :value="country.id"
              selected
            >
              {{ country.name }}
            </option>
          </select>

          <select
            v-if="query.country_id"
            v-model="query.city_id"
            class="form-control mr-3"
            @change="onCityChange()"
          >
            <option
              value="0"
              selected
            >
              None
            </option>

            <option
              v-for="city in cities.enabled"
              :key="city.id"
              :value="city.id"
              selected
            >
              {{ city.name }}
            </option>
          </select>

          <select
            v-if="query.city_id"
            v-model="query.theme_id"
            class="form-control"
            @change="onThemeChange()"
          >
            <option
              value="0"
              selected
            >
              None
            </option>

            <option
              v-for="themes in themes"
              :key="themes.id"
              :value="themes.id"
              selected
            >
              {{ themes.name }}
            </option>
          </select>
        </div>
      </div>

      <div class="text-dark">
        Total: {{ computedEntities.length }} places
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">
            #
          </th>
          <th scope="col">
            Name
          </th>
          <th scope="col">
            Venue set
          </th>
          <th scope="col">
            Local Rating
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(place, index) in computedEntities"
          :key="place.id"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>

          <td>
            <a
              href="#"
              @click.prevent="setPlaceView(place)"
            >
              {{ place.name }}
            </a>
          </td>

          <td>
            <a href="#">
              {{ isVenueSet(place) }}
            </a>
          </td>

          <td>
            <a href="#">
              {{ parseFloat(place.rating_local).toFixed(2) }}
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import {ENABLED} from '../states'

export default {
  data () {
    return {
      query: {
        country_id: 0,
        city_id: 0,
        theme_id: 0,
        rating: 10,
        offset: 0,
        limit: 100
      }
    }
  },

  computed: {
    ...mapGetters([
      'continents',
      'countries',
      'cities',
      'places',
      'themes'
    ]),

    computedEntities () {
      return this.places.filter(e => {
        return parseInt(e.city_id) === parseInt(this.query.city_id)
      })
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    await this.fetchCountries()
    await this.fetchCities()
    await this.fetchThemes()

    this.query.country_id = 0
    this.query.city_id = 0

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchEntities () {
      await this.$store.dispatch('fetchPlaces', {
        city_id: this.query.city_id,
        theme_id: this.query.theme_id,
        offset: this.query.offset,
        rating: this.query.rating,
        limit: this.query.limit
      })
    },

    async fetchThemes () {
      await this.$store.dispatch('fetchThemes')
    },

    async fetchCities () {
      await this.$store.dispatch('fetchCities', {
        state: ENABLED,
        country_id: this.query.country_id
      })
    },

    async fetchCountries () {
      await this.$store.dispatch('fetchCountries', {
        state: ENABLED
      })
    },

    async onCountryChange () {
      this.$store.commit('setLoading', true)

      await this.fetchCities()

      this.query.city_id = 0

      this.$store.commit('setLoading', false)
    },

    async onCityChange () {
      this.$store.commit('setLoading', true)

      this.query.theme_id = 0

      this.$store.commit('setLoading', false)
    },

    async onThemeChange () {
      this.$store.commit('setLoading', true)

      await this.fetchEntities()

      this.$store.commit('setLoading', false)
    },

    setPlaceView (place) {
      this.$store.commit('setPlace', place)

      this.$router.push('/place/' + place.id)
    },

    isVenueSet (place) {
      return place.venue_id ? 'Yes' : 'No'
    }
  }
}
</script>
