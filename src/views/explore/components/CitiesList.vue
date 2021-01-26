<template>
  <div class="explore-page">
    <div class="planning">
      <div class="container-fluid">
        <div class="planning-content">
          <div class="title">
            {{ __('Popular destinations') }}
          </div>

          <div
            class="theme-wrapper"
            style="position: relative;"
          >
            <a
              v-if="query"
              class="btn-theme-picker"
              href="#"
              @click.prevent="setThemePicker(true)"
            >
              <span>
                <i class="fa fa-th-large" />
                {{ __('Themes') }} ({{ query.themes.length }})
                <i class="fa fa-angle-down ml-4" />
              </span>
            </a>

            <theme-picker
              v-if="themePicker"
              v-on-clickaway="onClickOutThemePicker"
              :themes="themes"
              :selected="query.themes"
              style="right: 0;"
              @input="onInputThemes($event)"
              @hide="setThemePicker(false)"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="content">
        <div class="row">
          <city-card
            v-for="(city, index) in cities"
            :key="index"
            :index="index"
            :city="city"
          />
        </div>

        <observer v-if="!loadingMore && hasMoreCities" @intersect="fetchCities()" />

        <div class="text-center">
          <loading-circle
            :loading="loadingMore"
            :small="false"
            :color="'#FD753B'"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ThemePicker from '../../../components/themes/ThemePicker'
import CityCard from './CityCard'
import Observer from './Observer'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import { route } from '../../../mixins/route'

export default {
  components: {
    CityCard,
    Observer,
    ThemePicker
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      loadingMore: false,
      offset: 0,
      themePicker: false,
      hasMoreCities: true,
      limit: 4
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'cities',
      'themes'
    ])
  },

  methods: {
    async fetchCities () {
      this.loadingMore = true

      await this.$store.dispatch('fetchCities', {
        offset: this.offset,
        themes: this.query.themes.join(','),
        limit: this.limit
      })

      if (this.cities.length % this.limit || this.offset === this.cities.length) {
        this.hasMoreCities = false
      }

      this.offset = this.cities.length
      this.loadingMore = false
    },

    async onInputThemes (values) {
      this.loading(true)

      this.$store.commit('setQuery', {
        ...this.query,
        themes: values
      })

      this.$store.commit('setCities', [])
      this.hasMoreCities = true
      this.offset = 0

      this.fetchCities()

      this.loading(false)
    },

    setThemePicker (state) {
      this.themePicker = state
    },

    onClickOutThemePicker () {
      this.setThemePicker(false)
    },

    loading (state) {
      this.$emit('loading', state)
    }
  }
}
</script>
