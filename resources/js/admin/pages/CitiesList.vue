<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-4">
        Cities
      </h3>

      <toggle-input
        :enabled="query.state"
        :show-text="true"
        @input="onToggleState($event)"
      />
    </div>

    <div class="d-flex justify-content-between align-items-center my-2">
      <div>
        <select
          v-model="query.country_id"
          class="form-control"
          @change="onCountryFilterChange()"
        >
          <option
            v-if="!countries.enabled.length"
            selected
          >
            No available countries
          </option>
          <option
            v-else
            :value="OPTION_ALL"
            selected
          >
            All
          </option>

          <option
            v-for="country in countries.enabled"
            :key="country.id"
            :value="country.id"
          >
            {{ country.name }}
          </option>
        </select>
      </div>

      <div class="text-dark">
        Total: {{ computedEntities.length }} cities
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
            Disable / Enable
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(city, index) in computedEntities"
          :key="city.id"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>

          <td>
            <a
              href="#"
              @click.prevent="setPlaceView(city)"
            >
              {{ city.name }}
            </a>
          </td>

          <td>
            <toggle-input
              :enabled="city.enabled || false"
              @input="setState(city.id, $event)"
            />
          </td>
        </tr>
      </tbody>
    </table>

    <div
      v-if="canLoadMore"
      class="d-flex justify-content-center align-items-center my-2"
    >
      <a
        href="#"
        class="btn btn-outline-primary"
        @click.prevent="loadMore()"
      >Load more cities
      </a>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import {SLUG_CITIES} from '../slugs'
import {DISABLED, ENABLED, KEY_DISABLED, KEY_ENABLED} from '../states'
import {BASE_FETCH_LIMIT} from '../config'

export default {
  data () {
    return {
      query: {
        limit: BASE_FETCH_LIMIT,
        state: ENABLED,
        country_id: 0
      },
      canLoadMore: true,
      OPTION_ALL: 0
    }
  },

  computed: {
    ...mapGetters([
      'continents',
      'countries',
      'cities'
    ]),

    computedEntities () {
      return this.query.state
        ? this.cities.enabled
        : this.cities.disabled
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    await this.fetchContinents()
    await this.fetchCountries()

    if (!this.cities.enabled.length) {
      await this.fetchEntities(ENABLED)

      if (this.cities.enabled.length < BASE_FETCH_LIMIT) {
        this.canLoadMore = false
      }
    }

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchEntities (state = ENABLED) {
      await this.$store.dispatch('fetchCities', {
        ...this.query,
        state
      })
    },

    async fetchCountries () {
      await this.$store.dispatch('fetchCountries', {
        state: ENABLED
      })
    },

    async fetchContinents () {
      if (!this.continents.enabled.length) {
        await this.$store.dispatch('fetchContinents', {
          state: ENABLED
        })
      }
    },

    async setState (id, state) {
      this.$store.commit('setLoading', true)

      const entity = await this.$store.dispatch('setEntityState', {
        slug: SLUG_CITIES,
        id,
        state
      })

      if (entity) {
        if (state) {
          this.addEntity(entity, KEY_ENABLED)
          this.removeEntity(entity, KEY_DISABLED)
        } else {
          this.addEntity(entity, KEY_DISABLED)
          this.removeEntity(entity, KEY_ENABLED)
        }
      }

      this.$store.commit('setLoading', false)
    },

    async addEntity (entity, key) {
      if (!this.cities[key].length) {
        this.fetchEntities(DISABLED)
      }

      const cities = this.cities[key]

      cities.push(entity)

      await this.$store.commit('setCities', {
        key,
        data: cities
      })
    },

    async removeEntity (entity, key) {
      if (!this.cities[key].length) {
        this.fetchEntities(DISABLED)
      }

      const cities = this.cities[key]

      const index = cities.findIndex(e => {
        return parseInt(e.id) === parseInt(entity.id)
      })

      cities.splice(index, 1)

      await this.$store.commit('setCities', {
        key,
        data: cities
      })
    },

    async onToggleState (state) {
      this.query.state = state
      this.query.limit = BASE_FETCH_LIMIT
      this.canLoadMore = true

      let key = ENABLED
      if (!this.query.state) {
        key = DISABLED
      }

      this.$store.commit('setLoading', true)

      await this.fetchEntities(key)

      this.$store.commit('setLoading', false)
    },

    async loadMore () {
      this.$store.commit('setLoading', true)

      this.query.limit += BASE_FETCH_LIMIT

      let key = KEY_ENABLED
      if (!this.query.state) {
        key = KEY_DISABLED
      }

      const prevLength = this.cities[key].length

      await this.$store.dispatch('fetchCities', this.query)

      this.canLoadMore = (this.cities[key].length - prevLength) >= BASE_FETCH_LIMIT

      this.$store.commit('setLoading', false)
    },

    async onCountryFilterChange () {
      this.$store.commit('setLoading', true)

      this.canLoadMore = true

      this.query.limit = BASE_FETCH_LIMIT

      await this.$store.dispatch('fetchCities', this.query)

      const key = this.query.state ? KEY_ENABLED : KEY_DISABLED
      if (this.cities[key].length < BASE_FETCH_LIMIT) {
        this.canLoadMore = false
      }

      this.$store.commit('setLoading', false)
    },

    setPlaceView (place) {
      this.$router.push('/place/' + place.id)

      this.$store.commit('setPlace', place)
    }
  }
}
</script>
