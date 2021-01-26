<template>
  <div>
    <nav class="navbar navbar-expand-lg explore-navbar navbar-light bg-white">
      <div class="container-fluid">
        <a
          href="/"
          class="navbar-brand text-primary"
        >
          <img
            :src="logoUrl"
            class="product-logo"
            alt="Rutugo Logo"
          >
        </a>

        <button
          class="navbar-toggler"
          type="button"
          @click="toggleNavbar"
        >
          <span class="navbar-toggler-icon" />
        </button>

        <div
          id="navbarSupportedContent"
          class="collapse navbar-collapse"
          :class="{show: showNav}"
        >
          <ul class="navbar-nav mr-auto" />

          <form v-if="formVisible" class="form-inline mx-auto">
            <city-input
              :value="query.origin_city"
              :show-icon="false"
              :disabled="hasNotifications"
              class="mr-4"
              input-style="font-size: 13px; font-weight: 500; border: 1px solid #e6e6e6;"
              @input="onInputCity($event)"
            />

            <div class="form-group position-relative">
              <input
                :value="datePickerInputText"
                :disabled="hasNotifications"
                type="text"
                class="form-control mr-4"
                placeholder="A week in Month"
                style="font-size: 13px; font-weight: 500; border: 1px solid #e6e6e6;"
                @click.prevent="showDatePicked()"
              >

              <travel-date-picker
                v-if="datePicker"
                v-on-clickaway="onClickOutsideDatePicker"
                :value="actualQuery.date"
                @input="onDatePickerInput($event)"
                @hide="hideDatePicker"
              />
            </div>

            <div class="form-group position-relative">
              <input
                :value="travellersInputText"
                :disabled="hasNotifications"
                type="text"
                class="form-control"
                placeholder="Travellers"
                style="font-size: 13px; font-weight: 500; border: 1px solid #e6e6e6;"
                @click.prevent="showTravellersPicker()"
              >

              <travellers-picker
                v-if="travellersPicker"
                v-on-clickaway="onClickOutsideTravellersPicker"
                :value="actualQuery.travellers"
                style="top: 100%;"
                @input="onTravellersInput($event)"
                @hide="hideTravellersPicker()"
              />
            </div>

            <div
              v-if="currencies[currentCurrency]" class="form-group position-relative mr-3"
              style="margin-left: 20px;"
            >
              <div class="dropdown search-input-box">
                <a
                  class="btn language-selector"
                  @click.prevent="toggleCurrency = !toggleCurrency"
                >
                  <span>
                    {{ currencies[currentCurrency].symbol }}
                  </span>
                  {{ currencies[currentCurrency].code }}
                </a>

                <div
                  v-if="toggleCurrency"
                  v-on-clickaway="hideCurrencyPicker"
                  class="dropdown-results"
                >
                  <ul class="results-list">
                    <li
                      v-for="(currency, index) in currencies"
                      :key="index"
                      class="results-list-item"
                    >
                      <a
                        href="#"
                        @click.prevent="setCurrency(index)"
                      >
                        <span style="font-size: 12px;">
                          {{ currency.symbol }}
                        </span>
                        {{ currency.code }}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <div v-if="locales[currentLocale]" class="form-group position-relative">
              <div class="dropdown search-input-box">
                <a
                  class="btn language-selector"
                  @click.prevent="toggleLocale = !toggleLocale"
                >
                  <i class="fa fa-globe" />
                  {{ locales[currentLocale].code.toUpperCase() }}
                </a>

                <div
                  v-if="toggleLocale"
                  v-on-clickaway="hideLocalePicker"
                  class="dropdown-results"
                >
                  <ul class="results-list">
                    <li
                      v-for="(locale, index) in locales"
                      :key="index"
                      class="results-list-item"
                    >
                      <a
                        href="#"
                        @click.prevent="setLocale(index)"
                      >
                        {{ locale.code.toUpperCase() }}
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </form>

          <ul class="navbar-nav ml-auto">
            <li
              v-if="!isExploreRoute"
              class="nav-item"
              :class="{active: activeItem === 1}"
              @click="active(1)"
            >
              <a
                href="£"
                class="nav-link notification-area"
                @click.prevent="redirectTo('explore')"
              >
                {{ __('Cities') }}
              </a>
            </li>

            <li
              class="nav-item search-input-box"
              :class="{active: activeItem === 1}"
              @click.prevent="togglePlans = !togglePlans"
            >
              <a
                class="nav-link notification-area cursor-point-hover"
              >
                {{ __('My plans') }}

                <span
                  v-if="hasNotifications"
                  class="notification-circle"
                >{{ notifications.count }}</span>
              </a>

              <div
                v-if="togglePlans"
                v-on-clickaway="hidePlanPicker"
                class="dropdown-results"
                style="min-width: 120px; max-width: 120px;"
              >
                <ul class="results-list">
                  <li
                    v-if="!plans.length"
                    class="results-list-item"
                  >
                    <a>{{ __('No plans') }}</a>
                  </li>

                  <li
                    v-for="(plan, index) in plans"
                    :key="index"
                    class="results-list-item"
                  >
                    <a
                      href="#"
                      @click.prevent="redirectTo('booking')"
                    >
                      {{ plan }}
                    </a>
                  </li>
                </ul>
              </div>
            </li>

            <li
              v-if="!authenticated"
              class="nav-item"
            >
              <a
                class="nav-link"
                href="#"
                data-toggle="modal"
                data-target="#loginModal"
                @click.prevent="onLogin()"
              >
                {{ __('Log in') }}
              </a>
            </li>

            <li
              v-if="authenticated"
              :class="{active: activeItem === 100}"
              class="nav-item search-input-box user-nav"
            >
              <a
                href="#"
                class="nav-link notification-area"
                @click.prevent="toggleUserDropdown()"
              >
                {{ authUser.first_name }}

                <span
                  v-if="hasOrders"
                  class="notification-circle"
                >{{ notifications.orders }}</span>
              </a>

              <div
                v-if="userDropdown"
                v-on-clickaway="hideUserDropdown"
                class="dropdown-results"
                style="min-width: 120px; max-width: 120px; right: 0;"
              >
                <ul class="results-list">
                  <li
                    v-for="(order, index) in orders"
                    :key="index"
                    class="results-list-item"
                  >
                    <a href="#">{{ order.name }}</a>
                  </li>

                  <li class="results-list-item">
                    <a href="/logout">{{ __('Logout') }}</a>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <login-modal
      v-if="showLogin"
      @register="onRegister()"
      @close="showLogin = false"
    />

    <register-modal
      v-if="showRegister"
      @login="onLogin()"
      @close="showRegister = false"
    />
  </div>
</template>

<script>
import TravelDatePicker from './TravelDatePicker'
import TravellersPicker from './TravellersPicker'
import {cloneDeep} from 'lodash'
import LoginModal from '../modals/auth/LoginModal'
import RegisterModal from '../modals/auth/RegisterModal'
import CityInput from '../pages/welcome/components/CityInput'

import {LOGO_URL} from '../config'
import axios from 'axios'
import {mixin as clickaway} from 'vue-clickaway'
import * as moment from 'moment'
import {mapGetters} from 'vuex'
import { route } from '../mixins/route'

export default {
  components: {
    TravelDatePicker,
    TravellersPicker,
    CityInput,
    RegisterModal,
    LoginModal
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      orders: [],
      plans: [],
      togglePlans: false,
      userDropdown: false,
      currentLocale: 0,
      toggleLocale: false,
      toggleCurrency: false,
      currencies: [],
      currentCurrency: 0,
      locales: [],
      showLogin: false,
      showRegister: false,
      showResultsDropdown: false,
      searchResults: [],
      actualQuery: {
      },
      datePicker: false,
      travellersPicker: false,
      activeItem: false,
      showNav: false
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'authUser',
      'notifications'
    ]),

    hasNotifications () {
      return !!(this.notifications && this.notifications.count)
    },

    hasOrders () {
      return !!(this.notifications && this.notifications.orders)
    },

    isExploreRoute () {
      if (!this.$route) {
        return false
      }

      return this.$route.name === 'explore'
    },

    formVisible () {
      return !window.location.pathname.includes('booking')
    },

    authenticated () {
      return this.authUser !== false
    },

    logoUrl () {
      return LOGO_URL
    },

    travellersInputText () {
      const count = this.countTravellers()


      return count ? count.toString() + ' Travellers' : ''
    },

    datePickerInputText () {
      return this.actualQuery.date.date_string
    }
  },

  watch: {
    notifications: {
      handler () {
        //
      }
    }
  },

  created () {
    this.actualQuery = cloneDeep(this.query)
    this.fetchPlans()
    this.fetchCurrencies()
    this.fetchLocales()
    this.fetchOrders()

    this.processDateString()
  },

  methods: {
    async fetchOrders () {
      await axios
        .get(`/api/orders`)
        .then(r => {
          if (r.data.success) {
            this.orders = r.data.entities
          }
        })
    },

    onInputCity (value) {
      if (!value) {
        return false
      }

      this.actualQuery.origin_city = {
        ...this.actualQuery.origin_city,
        id: value
      }

      this.updateQuery()

      this.reloadPage()
    },

    async fetchCurrencies () {
      await axios
        .get(`/api/currencies`)
        .then(r => {
          this.currencies = r.data.entities

          this.currentCurrency = this.currencies.findIndex(e => {
            return e.code.toLowerCase() === this.query.currency.code.toLowerCase()
          })
        })
    },

    async fetchLocales () {
      await axios
        .get(`/api/languages`)
        .then(r => {
          this.locales = r.data.entities

          this.currentLocale = this.locales.findIndex(e => {
            return e.code.toLowerCase() === window._locale.toLowerCase()
          })
        })
    },

    async fetchPlans () {
      await axios
        .get(`/api/cart/plans`)
        .then(r => {
          if (r.data.success) {
            this.plans = r.data.entities.map(e => {
              return e.name
            })
          }
        })
    },

    redirectTo (route) {
      this.$redirect(route, this.routeParams)
    },

    hideCurrencyPicker () {
      this.toggleCurrency = false
    },

    hidePlanPicker () {
      this.togglePlans = false
    },

    hideLocalePicker () {
      this.toggleLocale = false
    },

    processDateString () {
      const date = this.actualQuery.date
      let dateString = ''

      if (date.date_type === this.$const('DATE_TYPE_EXACT')) {
        dateString = moment(date.start_date).format('ddd, DD MMM')
          + ' to '
          + moment(date.end_date).format('ddd, DD MMM')
      } else {
        const start = date.start_date
        const month = moment(start).format('MMM')

        const temp = `${month} ${moment(start).year()}`

        this.$const('TRIP_TYPES').forEach(e => {
          if (e.value === date.date_type) {
            dateString = `${temp} - ${e.text}`
          }
        })
      }

      this.actualQuery.date.date_string = dateString
    },

    setLocale (index) {
      this.currentLocale = index
      this.toggleLocale = false

      let hash = ''
      if (this.$route) {
        hash = this.$route.fullPath
      }

      this.$redirect('',
        this.routeParams,
        hash,
        this.locales[this.currentLocale].code
      )
    },

    setCurrency (index) {
      this.currentCurrency = index
      this.toggleCurrency = false

      let hash = ''
      if (this.$route) {
        hash = this.$route.fullPath
      }

      this.$redirect('',
        {
          ...this.routeParams,
          currency: this.currencies[this.currentCurrency].code
        },
        hash
      )
    },

    reloadPage (){
      let hash = ''
      if (this.$route) {
        hash = this.$route.fullPath
      }

      this.$redirect('',
        {
          ...this.routeParams,
          currency: this.currencies[this.currentCurrency].code
        },
        hash
      )
    },

    hideUserDropdown () {
      this.userDropdown = false
      this.active(false)
    },

    toggleUserDropdown () {
      this.userDropdown = !this.userDropdown

      if (this.userDropdown) {
        this.active(100)
      } else {
        this.active(false)
      }
    },

    themesInputText () {
      const count = Object.keys(this.actualQuery.themes).length


      return count ? count + ' Themes' : ''
    },

    active (id) {
      this.activeItem = id
    },

    selectPlace (index) {
      this.actualQuery.originCity = this.searchResults[index]
      this.showResultsDropdown = false
    },

    updateQuery () {
      this.$store.commit('setQuery', cloneDeep(this.actualQuery))
    },

    onClickOutsideTravellersPicker () {
      this.travellersPicker = false
    },

    onTravellersInput (value) {
      this.actualQuery.travellers = {
        ...value
      }

      this.updateQuery()

      this.reloadPage()
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    onDatePickerInput (value) {
      this.actualQuery.date = cloneDeep(value)

      this.datePicker = false

      this.updateQuery()

      this.reloadPage()
    },

    onClickOutsideDatePicker () {
      this.datePicker = false
    },

    showTravellersPicker () {
      this.travellersPicker = true
    },

    countTravellers () {
      return this.actualQuery.travellers.adults
          + this.actualQuery.travellers.children
          + this.actualQuery.travellers.infants
    },

    searchOriginPlace (evt) {
      const searchValue = evt.target.value
      if (!searchValue || searchValue === '') {
        return false
      }

      axios
        .get(`/api/cities/search`, {
          params: {
            query: searchValue
          }
        })
        .then(r => {
          if (r.data.success) {
            this.searchResults = r.data.places
          }
        })

      this.showResultsDropdown = true
    },

    showDatePicked () {
      this.datePicker = true
    },

    hideDatePicker () {
      this.datePicker = false
    },

    onRegister () {
      this.showRegister = true
      this.showLogin = false
    },

    onLogin () {
      this.showLogin = true
      this.showRegister = false
    },

    toggleNavbar () {
      this.showNav = !this.showNav
    }
  }
}
</script>
