<template>
  <div v-if="city">
    <city-page-header
      :previous-city="previousCity"
      :current-city="city"
      :next-city="nextCity"
    />

    <div class="container-fluid">
      <div class="city-page">
        <div class="info">
          <div class="about">
            <h4 class="title">
              {{ __('About') }}
            </h4>

            <div>
              {{ city.description }}
            </div>
          </div>

          <weather-box
            v-if="hasWeather"
            :weathers="city.weathers"
          />
        </div>

        <div class="planning">
          <div class="planning-content">
            <div class="title">
              {{ __('Popular in') }} {{ city.name }}
            </div>

            <div
              class="theme-wrapper"
              style="position: relative;"
            >
              <a
                class="btn-theme-picker blob blue"
                href="#"
                @click.prevent="showThemePicker()"
              >
                <span>
                  <i class="fa fa-th-large" />
                  {{ __('Themes') }} ({{ themesCount }})
                  <i class="fa fa-angle-down ml-4" />
                </span>
              </a>

              <theme-picker
                v-if="themePicker"
                v-on-clickaway="onClickOutsideThemePicker"
                :themes="themes"
                :selected="actualQuery.themes"
                style="right: 0;"
                @input="onThemesInput($event)"
                @hide="hideThemePicker()"
              />
            </div>
          </div>
        </div>

        <div class="themes">
          <nav class="nav list">
            <a
              v-for="(theme, index) in selectedThemes"
              :key="index"
              :class="{'active': theme.id === currentTheme}"
              class="nav-link theme"
              @click="setCurrentTheme(theme.id)"
            >
              {{ theme.name }}
            </a>
          </nav>

          <toggle-input
            :enabled="addedToCart"
            :show-text="true"
            :left-text="'Available'"
            :right-text="'Added'"
            @input="addedToCart = !!$event"
          />
        </div>

        <theme-row
          v-for="(theme, index) in selectedThemes"
          :ref="theme.id"
          :key="index"
          :added-to-cart="addedToCart"
          :theme="theme"
          :city-id="cityId"
        />
      </div>
    </div>
  </div>
</template>

<script>
import CityPageHeader from './CityPageHeader'
import ThemeRow from '../../../components/ThemeRow'
import ThemePicker from '../../../components/ThemePicker'
import ToggleInput from '../../../../components/ToggleInput'
import WeatherBox from './WeatherBox'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import {cloneDeep} from 'lodash'
import { route } from '../../../mixins/route'

export default {
  components: {
    WeatherBox,
    CityPageHeader,
    ToggleInput,
    ThemeRow,
    ThemePicker
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      cityId: false,
      currentTheme: false,
      actualQuery: {
      },
      addedToCart: false,
      previousCity: null,
      nextCity: null,
      themePicker: false,
      cityThemes: {
      }
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'themes',
      'cities',
      'cart',
      'currentPlanId',
      'city'
    ]),

    hasWeather () {
      return !!Object.keys(this.city.weathers).length
    },

    selectedThemes () {
      return this.themes.filter(i => {
        return this.actualQuery.themes.includes(i.id)
      })
    },

    extraStyle () {
      return {
        backgroundImage: 'url(' + this.cityImage(this.city) + ')'
      }
    },

    themesCount () {
      return Object.keys(this.actualQuery.themes).length
    }
  },

  watch: {
    $route: {
      async handler (val) {
        if (val.params.id && this.cities.length) {
          this.cityId = parseInt(val.params.id)
          await this.processCity(this.cityId)
        }
      },
      immediate: true
    }
  },

  created () {
    this.$store.commit('setLoading', true)

    this.actualQuery = cloneDeep(this.query)

    this.$store.commit('setLoading', false)
  },

  methods: {
    setCurrentTheme (id) {
      let element = this.$refs[id]
      let top = element[0].$el.offsetTop

      window.scrollTo({
        top,
        behavior: 'smooth'
      })

      this.currentTheme = id
    },

    cityImage (city) {
      if (!city.image_url) {
        return ''
      }

      return `/storage/${city.image_url}`
    },

    init () {
      if (!this.authUser && this.data.user) {
        this.$store.commit('setAuthUser', this.data.user)
      }

      this.$store.commit('setThemes', this.data.themes)
      this.$store.commit('setQuery', this.data.query)
    },

    processCity (id) {
      let index = this.cities.findIndex(i => i.id.toString() === id.toString())

      if (index === -1) {
        index = 1
      }

      this.$store.commit('setCity', this.cities[index])

      let prevIndex = index - 1
      let nextIndex = index + 1

      if (prevIndex < 0) {
        prevIndex = this.cities.length - 1
      }

      if (nextIndex > this.cities.length - 1) {
        nextIndex = 0
      }

      if (index !== prevIndex) {
        this.previousCity = this.cities[prevIndex]
      }

      if (index !== nextIndex) {
        this.nextCity = this.cities[nextIndex]
      }
    },

    async onThemesInput (values) {
      this.$store.commit('setLoading', true)
      this.actualQuery.themes = cloneDeep(values)
      await this.$store.commit('setQuery', this.actualQuery)

      this.$redirect('explore', this.routeParams, `/city/${this.city.id}`)
    },

    showThemePicker () {
      this.themePicker = true
    },
    hideThemePicker () {
      this.themePicker = false
    },
    onClickOutsideThemePicker () {
      this.themePicker = false
    }
  }
}
</script>
