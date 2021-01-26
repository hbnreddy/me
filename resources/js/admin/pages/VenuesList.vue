<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-2">
        Musement Venues
      </h3>

      <toggle-input
        :enabled="query.state"
        :show-text="true"
        :left-text="'Unassigned'"
        :right-text="'Assigned'"
        @input="onToggleState($event)"
      />
    </div>

    <div class="d-flex justify-content-end align-items-center my-2">
      <div class="mr-2">
        <span v-if="query.state">{{ totalAssigned }} assigned</span>
        <span v-else>{{ totalUnassigned }} unassigned</span>
        of {{ totalCount }} venues
      </div>

      <a
        v-if="toggleImport"
        href="#"
        class="btn btn-danger text-white mr-2"
        @click.prevent="toggleImportForm()"
      >Hide this
      </a>

      <a
        v-else
        href="#"
        class="btn btn-primary text-white mr-2"
        @click.prevent="toggleImportForm()"
      >Import
      </a>

      <a
        href="/venues/export"
        class="btn btn-primary text-white"
      >Export
      </a>
    </div>

    <form
      v-if="toggleImport"
      :action="'/venues/import'"
      method="post"
      class="d-flex justify-content-between align-items-center my-2"
      enctype="multipart/form-data"
    >
      <input
        :value="csrf()"
        type="hidden"
        name="_token"
      >

      <div class="form-group">
        <label for="import-file">Import venues</label>
        <input
          id="import-file"
          name="import_file"
          type="file"
          class="form-control-file"
        >
      </div>

      <button
        type="submit"
        class="btn btn-primary text-white"
      >
        Proceed
      </button>
    </form>

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
            Place assigned
          </th>
          <th scope="col" />
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(venue, index) in computedEntities"
          :key="venue.id"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>

          <td>
            <a href="#">
              {{ venue.name }}
              <span v-if="venue.city">({{ venue.city.name }}, {{ venue.city.country.name }})</span>
            </a>
          </td>

          <td>
            <a href="#">
              {{ venue.place_id && venue.place ? venue.place.name : '-' }}
            </a>
          </td>

          <td>
            <a
              href="#"
              class="btn btn-outline-primary"
              @click.prevent="setPlace(venue.id)"
            >Set POI
            </a>
          </td>
        </tr>
      </tbody>
    </table>

    <paginate
      v-if="pagesCount > 1"
      :page-count="pagesCount"
      :click-handler="onChangePage"
      :prev-text="'Previous'"
      :next-text="'Next'"
      :page-range="3"
      :margin-pages="2"
      :container-class="'pagination'"
      :page-class="'page-item'"
    />

    <set-place-modal
      v-if="showPlaceModal"
      :venue-id="venueId"
      :visible="true"
      @close="hidePlaceModal()"
    />
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import SetPlaceModal from '../modals/SetPlaceModal'
import {ENABLED} from '../states'

export default {
  components: {
    SetPlaceModal
  },

  data () {
    return {
      query: {
        state: ENABLED,
        offset: 0,
        limit: 10
      },
      toggleImport: false,
      venueId: false,
      showPlaceModal: false,
      totalAssigned: 0,
      totalUnassigned: 0,
      totalCount: 0
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

    computedEntities () {
      if (this.query.state) {
        return Object.values(this.venues).filter(e => {
          return e.place_id !== 0
        })
      }

      return Object.values(this.venues).filter(e => {
        return e.place_id === 0
      })
    },

    pagesCount () {
      const total = this.query.state ? this.totalAssigned : this.totalUnassigned
      let count = parseInt(total / this.query.limit)

      if (this.total !== this.query.limit) {
        count++
      }

      return count
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    await this.fetchContinents()
    await this.fetchCountries()
    await this.fetchCities()

    if (!this.venues.length) {
      await this.fetchEntities()
    }

    const total = await this.$store.dispatch('venuesTotalCount')

    this.totalAssigned = total.total_assigned
    this.totalUnassigned = total.total_unassigned

    this.totalCount = this.totalUnassigned + this.totalAssigned

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchEntities () {
      await this.$store.dispatch('fetchVenues', this.query)
    },

    async fetchCities () {
      await this.$store.dispatch('fetchCities', {
        state: ENABLED
      })
    },

    async fetchCountries () {
      await this.$store.dispatch('fetchCountries', {
        state: ENABLED
      })
    },

    async fetchContinents () {
      await this.$store.dispatch('fetchContinents', {
        state: ENABLED
      })
    },


    toggleImportForm () {
      this.toggleImport = !this.toggleImport
    },

    async onChangePage (pageNumber) {
      this.$store.commit('setLoading', true)

      this.query.offset = (pageNumber - 1) * this.query.limit

      await this.fetchEntities()

      this.$store.commit('setLoading', false)
    },

    setPlace (venueId) {
      this.venueId = venueId
      this.showPlaceModal = true
    },

    hidePlaceModal () {
      this.showPlaceModal = false
    },

    async onToggleState (state) {
      this.$store.commit('setLoading', true)

      this.query.state = state

      await this.fetchEntities()

      this.$store.commit('setLoading', false)
    },

    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>
