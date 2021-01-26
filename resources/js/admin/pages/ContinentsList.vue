<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-4">
        Continents
      </h3>

      <toggle-input
        :enabled="query.state"
        :show-text="true"
        @input="onToggleState($event)"
      />
    </div>

    <div class="d-flex justify-content-end align-items-center my-2">
      <div class="text-dark">
        Total: {{ computedEntities.length }} continents
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
          v-for="(continent, index) in computedEntities"
          :key="continent.id"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>

          <td>
            <a
              href="#"
              @click.prevent="setPlaceView(continent)"
            >
              {{ continent.name }}
            </a>
          </td>

          <td>
            <toggle-input
              :enabled="continent.enabled || false"
              @input="setState(continent.id, $event)"
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
import {SLUG_CONTINENTS} from '../slugs'

export default {
  data () {
    return {
      query: {
        state: ENABLED
      }
    }
  },

  computed: {
    ...mapGetters([
      'continents'
    ]),

    computedEntities () {
      return this.query.state
        ? this.continents.enabled
        : this.continents.disabled
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    if (!this.continents.enabled.length) {
      this.fetchEntities(ENABLED)
    }

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchEntities (state = ENABLED) {
      await this.$store.dispatch('fetchContinents', {
        state
      })
    },

    async setState (id, state) {
      this.$store.commit('setLoading', true)

      const entity = await this.$store.dispatch('setEntityState', {
        slug: SLUG_CONTINENTS,
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
      if (!this.continents[key].length) {
        this.fetchEntities(DISABLED)
      }

      const continents = this.continents[key]

      continents.push(entity)

      await this.$store.commit('setContinents', {
        key,
        data: continents
      })
    },

    async removeEntity (entity, key) {
      if (!this.continents[key].length) {
        this.fetchEntities(DISABLED)
      }

      const continents = this.continents[key]

      const index = continents.findIndex(e => {
        return parseInt(e.id) === parseInt(entity.id)
      })

      continents.splice(index, 1)

      await this.$store.commit('setContinents', {
        key,
        data: continents
      })
    },

    async onToggleState (state) {
      this.query.state = state

      if (!this.continents.disabled.length) {
        this.$store.commit('setLoading', true)

        await this.fetchEntities(DISABLED)

        this.$store.commit('setLoading', false)
      }
    },

    setPlaceView (place) {
      this.$router.push('/place/' + place.id)

      this.$store.commit('setPlace', place)
    }
  }
}
</script>
