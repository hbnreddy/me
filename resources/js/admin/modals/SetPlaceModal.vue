<template>
  <modal
    :visible="visible"
    :size="'modal-lg'"
    class="d-block"
    @close="emitClose(false)"
  >
    <h5
      slot="modal-title"
      class="modal-title"
    >
      {{ currentVenue.name }}<br>
      <span style="color: #d3d3d3;">{{ currentVenue.city_name }}, {{ currentVenue.country_name }}</span>
    </h5>

    <div
      slot="modal-body"
      class="modal-body-content"
      style="max-height: 68vh; overflow: auto;"
    >
      <div class="d-flex justify-content-between align-items-center my-2">
        <select
          v-model="query.country_id"
          class="form-control mr-3"
          aria-label=""
          @change="onCountryChange()"
        >
          <option
            v-if="!countries.length"
            value="0"
            selected
          >
            No available countries
          </option>

          <option
            v-for="country in allCountries"
            :key="country.id"
            :value="country.id"
            selected
          >
            {{ country.name }}
          </option>
        </select>

        <select
          v-model="query.city_id"
          class="form-control mr-3"
          aria-label=""
          @change="onCityChange()"
        >
          <option
            v-if="!cities.length"
            value="0"
            selected
          >
            No available cities
          </option>

          <option
            v-for="city in allCities"
            :key="city.id"
            :value="city.id"
            selected
          >
            {{ city.name }}
          </option>
        </select>
      </div>

      <div class="d-flex align-items-center mb-2">
        <div class="mr-3">
          <i
            class="fa fa-search"
            style="font-size: 20px;"
          />
        </div>

        <input
          v-model="query.search"
          class="form-control form-control-borderless mr-2"
          type="text"
          placeholder="Search places by words and sub-words"
          @keyup.enter.prevent="searchPlace()"
        >
      </div>

      <ul
        v-if="places.length"
        class="list-group"
      >
        <li
          v-for="(place, index) in filteredPlaces"
          :key="index"
          class="list-group-item"
        >
          <div class="d-flex justify-content-between align-items-center">
            <div>{{ place.name }}</div>

            <a
              :class="placeId === place.id ? 'btn-success text-white' : 'btn-outline-primary'"
              href="#"
              class="btn"
              @click.prevent="selectPlace(place.id)"
            >Select
            </a>
          </div>
        </li>
      </ul>
    </div>

    <div
      slot="modal-footer"
      class="modal-footer"
    >
      <button
        v-if="placeId !== 0"
        class="btn btn-block btn-orange"
        @click.prevent="save()"
      >
        Save
      </button>
    </div>
  </modal>
</template>

<script>
import {mapGetters} from 'vuex'
import Modal from '../../components/Modal'
import { debounce } from 'lodash'

export default {
  components: {
    Modal
  },

  props: {
    venueId: {
      type: Number,
      required: true
    },

    visible: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      filteredPlaces: [],
      query: {
        search: '',
        limit: 100,
        country_id: false,
        city_id: false
      },
      allCountries: [],
      allCities: [],
      placeId: 0
    }
  },

  computed: {
    ...mapGetters([
      'continents',
      'countries',
      'cities',
      'places',
      'venues'
    ]),

    currentVenue () {
      return this.venues.find(i => {
        return parseInt(i.id) === parseInt(this.venueId)
      })
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    this.query.country_id = this.currentVenue.country_id
    this.query.city_id = this.currentVenue.city_id

    this.placeId = this.currentVenue.place_id ? this.currentVenue.place_id : 0

    this.allCountries = [
      ...this.countries.enabled,
      ...this.countries.disabled
    ]

    this.filterCities()

    await this.fetchPlaces()

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchPlaces () {
      await this.$store.dispatch('fetchPlaces', {
        ...this.query,
        venue_not_set: false
      })

      this.filteredPlaces = this.places
    },

    filterCities () {
      this.allCities = [
        ...this.cities.enabled.filter(e => {
          return parseInt(e.country_id) === parseInt(this.query.country_id)
        }),
        ...this.cities.disabled.filter(e => {
          return parseInt(e.country_id) === parseInt(this.query.country_id)
        })
      ]
    },

    async onCountryChange () {
      if (!this.query.country_id) {
        return false
      }

      this.$store.commit('setLoading', true)

      await this.filterCities()

      this.query.city_id = this.allCities.length ? this.allCities[0].id : 0

      await this.fetchPlaces()

      this.$store.commit('setLoading', false)
    },

    async onCityChange () {
      if (!this.query.city_id) {
        return false
      }

      this.$store.commit('setLoading', true)

      await this.fetchPlaces()

      this.$store.commit('setLoading', false)
    },

    selectPlace (placeId) {
      if (this.placeId === placeId) {
        this.placeId = 0
      } else {
        this.placeId = placeId
      }
    },

    async save () {
      if (parseInt(this.placeId) === 0) {
        return false
      }

      const response = await this.$store.dispatch('setVenuePlace', {
        id: this.currentVenue.id,
        place_id: this.placeId
      })

      if (response.success) {
        const venues = [
          ...this.venues
        ]

        const index = venues.findIndex(e => {
          return parseInt(e.id) === parseInt(this.currentVenue.id)
        })

        venues.splice(index, 1, response.entity)

        this.$store.commit('setVenues', venues)
      }

      this.emitClose()
    },

    async searchPlace () {
      const search = debounce(() => {
        this.$store.commit('setLoading', true)

        this.query.search = this.query.search.trim()

        if (!this.query.search) {
          this.filteredPlaces = this.places

          return false
        }

        const places = [
          ...this.places
        ]

        const search = this.query.search.toString().toLowerCase()
        this.filteredPlaces = places.filter(e => {
          return e.name.toString().toLowerCase()
            .includes(search)
        })

        this.$store.commit('setLoading', false)
      }, 500)

      search()
    },

    emitClose () {
      this.$emit('close')
    }
  }
}
</script>
