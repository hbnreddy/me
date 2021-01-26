<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-2">
        Places Markers
      </h3>

      <toggle-input
        :enabled="query.state"
        :show-text="true"
        :left-text="'Unassigned'"
        :right-text="'Assigned'"
        @input="onToggleState()"
      />
    </div>

    <div class="d-flex justify-content-between align-items-center my-2">
      <div
        class="d-flex justify-content-between align-items-center mr-2"
        style="flex: 2;"
      >
        <select
          v-model="query.theme_id"
          class="form-control mr-3"
          style="max-width: 250px;"
          @change="onThemeChange()"
        >
          <option
            v-if="!themes.length"
            selected
          >
            No available themes
          </option>
          <option
            value="0"
            selected
          >
            All themes
          </option>

          <option
            v-for="theme in themes"
            :key="theme.id"
            :value="theme.id"
            selected
          >
            {{ theme.name }}
          </option>
        </select>

        <div class="mr-3">
          <i
            class="fa fa-search"
            style="font-size: 20px;"
          />
        </div>

        <input
          v-model="query.search"
          class="form-control form-control-borderless mr-2"
          type="search"
          placeholder="Search markers or keywords"
          @keyup.enter.prevent="search"
        >

        <a
          href="#"
          class="btn btn-success text-white"
          @click.prevent="search()"
        >Search
        </a>
      </div>

      <div class="d-flex justify-content-end align-items-center my-2">
        <a
          v-if="showImportRow"
          href="#"
          class="btn btn-danger text-white"
          @click.prevent="showImport()"
        >Hide this
        </a>

        <a
          v-else
          href="#"
          class="btn btn-primary text-white"
          @click.prevent="showImport()"
        >Import markers
        </a>
      </div>
    </div>

    <div class="d-flex justify-content-end align-items-center my-2">
      Total: {{ computedMarkers.length }} markers
    </div>

    <form
      v-if="showImportRow"
      :action="'/markers/import'"
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
        <label for="import-file">Import markers</label>
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
        Import
      </button>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">
            #
          </th>
          <th scope="col" />
          <th scope="col">
            Name
          </th>
          <th scope="col" />
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(marker, index) in computedMarkers"
          :key="index"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>
          <th
            scope="row"
            style="background-color: #0C66F1; text-align: center;"
          >
            <img
              :src="marker.picture"
              width="32px"
              height="32px"
            >
          </th>
          <td>
            <a href="#">
              {{ marker.name }}
            </a>
          </td>
          <td>
            <select
              :value="marker.theme_id"
              class="form-control"
              @change="onChange(marker.id, $event.target.value)"
            >
              <option
                v-if="!themes.length"
                :value="0"
                selected
              >
                No available themes
              </option>
              <option :value="0">
                None
              </option>

              <option
                v-for="theme in themes"
                :key="theme.id"
                :value="theme.id"
              >
                {{ theme.name }}
              </option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
  data () {
    return {
      query: {
        theme_id: 0,
        search: '',
        state: true
      },
      showImportRow: false,
      tempMarker: {
        id: false,
        theme_id: '',
        name: ''
      }
    }
  },

  computed: {
    ...mapGetters([
      'themes',
      'markers'
    ]),

    computedMarkers () {
      return this.query.state
        ? this.markers.filter(e => {
          return e.theme_id !== 0
        })
        : this.markers.filter(e => {
          return e.theme_id === 0
        })
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    await this.fetchThemes()

    if (!this.markers.length) {
      await this.fetchEntities()
    }

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchEntities () {
      await this.$store.dispatch('fetchMarkers', this.query)
    },

    async fetchThemes () {
      if (!this.themes.length) {
        await this.$store.dispatch('fetchThemes')
      }
    },

    async onChange (marker_id, theme_id) {
      this.$store.commit('setLoading', true)

      const success = await this.$store.dispatch('setMarkerTheme', {
        id: marker_id,
        theme_id
      })

      if (success) {
        const markers = this.markers

        const index = markers.findIndex(e => {
          return parseInt(marker_id) === parseInt(e.id)
        })

        const temp = {
          ...markers[index],
          theme_id
        }

        markers.splice(index, 1, temp)

        this.$store.commit('setMarkers', markers)
      } else {
        alert('Something went wrong! Please try again.')
      }

      this.$store.commit('setLoading', false)
    },

    async onThemeChange () {
      this.$store.commit('setLoading', true)

      await this.$store.dispatch('fetchMarkers', {
        theme_id: this.query.theme_id
      })

      this.$store.commit('setLoading', false)
    },

    onToggleState () {
      this.query.state = !this.query.state
    },

    showImport () {
      this.showImportRow = !this.showImportRow
    },

    shouldShow (marker) {
      if (this.query.state) {
        return parseInt(marker.theme_id) !== 0
      }

      return parseInt(marker.theme_id) === 0
    },

    async search () {
      await this.fetchEntities()
    },

    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>
