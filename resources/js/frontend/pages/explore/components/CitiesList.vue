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
              class="btn-theme-picker"
              href="#"
              @click.prevent="setThemePicker(true)"
            >
              <span>
                <i class="fa fa-th-large" />
                {{ __('Themes') }} ({{ actualQuery.themes.length }})
                <i class="fa fa-angle-down ml-4" />
              </span>
            </a>

            <theme-picker
              v-if="themePicker"
              v-on-clickaway="onClickOutThemePicker"
              :themes="themes"
              :selected="actualQuery.themes"
              style="right: 0;"
              @input="onThemesInput($event)"
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
            :city="city"
          />
        </div>

        <observer @intersect="loadMoreCities()" />

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
import ThemePicker from '../../../components/ThemePicker'
import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import {cloneDeep} from 'lodash'
import CityCard from './CityCard'
import axios from 'axios'
import Observer from './Observer'
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
      offset: 8,
      actualQuery: {
      },
      themePicker: false
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'cities',
      'themes'
    ])
  },

  created () {
    this.loading(true)

    this.actualQuery = cloneDeep(this.query)

    this.loading(false)
  },

  methods: {
    async loadMoreCities () {
      this.loadingMore = true

      await axios
        .get(`/api/cities/explore-more`, {
          params: {
            offset: this.offset,
            themes: this.actualQuery.themes.join('-')
          }
        })
        .then(r => {
          const ids = this.cities.map(c => c.id)

          let temp = [
            ...this.cities,
            ...r.data.entities.filter(e => {
              return !ids.includes(e.id)
            })
          ]

          this.$store.commit('setCities', temp)

          this.offset = temp.length

          this.loadingMore = false
        })
    },

    explore () {
      this.$redirect('explore', this.routeParams)
    },

    async onThemesInput (values) {
      this.loading(true)

      this.actualQuery.themes = values

      this.explore()

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
