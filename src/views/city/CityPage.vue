<template>
  <div v-if="city" class="city-page">
    <city-page-header
      :previous-city="previousCity"
      :city="city"
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
                class="btn-theme-picker blue"
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
                :selected="query.themes"
                style="right: 0;"
                @input="onInputThemes($event)"
                @hide="hideThemePicker()"
              />
            </div>
          </div>
        </div>
        <themes-block
          :cityId="city.id"
          :themes="selectedThemes"
        />
      </div>
    </div>
  </div>
</template>

<script>
import CityPageHeader from './CityPageHeader'
import ThemePicker from '../../components/themes/ThemePicker'
import WeatherBox from '../explore/components/WeatherBox'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import { route } from '../../mixins/route'
import ThemesBlock from '../../components/themes/ThemesBlock'

export default {
  components: {
    WeatherBox,
    ThemesBlock,
    CityPageHeader,
    ThemePicker
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      city: null,
      previousCity: null,
      nextCity: null,
      themePicker: false,
      cityThemes: {
      }
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId',
      'query',
      'themes',
      'cities',
      'cart'
    ]),

    hasWeather () {
      if (!this.city.weathers) {
        return false
      }

      return !!Object.keys(this.city.weathers).length
    },

    selectedThemes () {
      return this.themes.filter(i => {
        return this.query.themes.includes(i.id)
      })
    },

    extraStyle () {
      return {
        backgroundImage: 'url(' + this.cityImage(this.city) + ')'
      }
    },

    themesCount () {
      return Object.keys(this.query.themes).length
    }
  },

  watch: {
    $route: {
      handler () {
        this.fetchCity()
      },
      immediate: true
    }
  },

  methods: {
    async fetchCity () {
      const index = this.$route.params.index

      const cities = await this.$store.dispatch('fetchCities', {
        offset: index - 1,
        limit: 3,
        themes: this.query.themes.join(','),
        store: false
      })

      console.log(cities,"citr");

      this.previousCity = {
        ...cities[0],
        index: index - 1
      }
      this.city = {
        ...cities[1],
        index: index
      }
      this.nextCity = {
        ...cities[2],
        index: index + 1
      }
    },

    onThemeRowLoaded (id) {
      const index = this.themes.findIndex(e => {
        return e.id === id
      })

      if (index >= 0) {
        this.themes.splice(index, 1, {
          ...this.themes[index],
          loaded: true
        })
      }
    },

    cityImage (city) {
      if (!city.image_url) {
        return ''
      }

      return require(`../../assets/images/${city.image_url}`);
    },

    init () {
      if (!this.user && this.data.user) {
        this.$store.commit('setUser', this.data.user)
      }

      this.$store.commit('setThemes', this.data.themes)
      this.$store.commit('setQuery', this.data.query)
    },

    async onInputThemes (values) {
      this.$store.commit('setQuery', {
        ...this.query,
        themes: values
      })

      this.$router.replace({
        name: 'city',
        query: {
          ...this.routeQuery,
          themes: values.join(',')
        }
      })
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
