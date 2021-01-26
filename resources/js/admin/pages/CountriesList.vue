<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-4">
        Countries
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
          v-model="query.continent_id"
          class="form-control"
          @change="onContinentFilterChange()"
        >
          <option
            v-if="!continents.enabled.length"
            selected
          >
            No available continents
          </option>

          <option
            v-for="continent in continents.enabled"
            :key="continent.id"
            :value="continent.id"
            selected
          >
            {{ continent.name }}
          </option>
        </select>
      </div>

      <div class="text-dark">
        Total: {{ computedEntities.length }} countries
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
          v-for="(country, index) in computedEntities"
          :key="country.id"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>

          <td>
            <a
              href="#"
              @click.prevent="setPlaceView(country)"
            >
              {{ country.name }}
            </a>
          </td>

          <td>
            <toggle-input
              :enabled="country.enabled || false"
              @input="setState(country.id, $event)"
            />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import {DISABLED, ENABLED, KEY_DISABLED, KEY_ENABLED} from '../states'
import {SLUG_COUNTRIES} from '../slugs'

export default {
  data () {
    return {
      query: {
        state: ENABLED,
        continent_id: false
      }
    }
  },

  computed: {
    ...mapGetters([
      'continents',
      'countries'
    ]),

    computedEntities () {
      return this.query.state
        ? this.countries.enabled
        : this.countries.disabled
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    await this.fetchContinents()

    if (this.continents.enabled.length) {
      this.query.continent_id = this.continents.enabled[0].id
    }

    await this.fetchEntities(ENABLED)

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchEntities (state = ENABLED) {
      await this.$store.dispatch('fetchCountries', {
        ...this.query,
        state
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
        slug: SLUG_COUNTRIES,
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
      if (!this.countries[key].length) {
        this.fetchEntities(DISABLED)
      }

      const countries = this.countries[key]

      countries.push(entity)

      await this.$store.commit('setCountries', {
        key,
        data: countries
      })
    },

    async removeEntity (entity, key) {
      if (!this.countries[key].length) {
        this.fetchEntities(DISABLED)
      }

      const countries = this.countries[key]

      const index = countries.findIndex(e => {
        return parseInt(e.id) === parseInt(entity.id)
      })

      countries.splice(index, 1)

      await this.$store.commit('setCountries', {
        key,
        data: countries
      })
    },

    async onToggleState (state) {
      this.query.state = state

      let key = ENABLED
      if (!this.query.state) {
        key = DISABLED
      }

      this.$store.commit('setLoading', true)

      await this.fetchEntities(key)

      this.$store.commit('setLoading', false)
    },

    async onContinentFilterChange () {
      this.$store.commit('setLoading', true)

      await this.$store.dispatch('fetchCountries', this.query)

      this.$store.commit('setLoading', false)
    },

    setPlaceView (place) {
      this.$store.commit('setPlace', place)

      this.$router.push('/place/' + place.id)
    }
  }
}
</script>
