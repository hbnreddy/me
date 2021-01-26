<template>
  <div>
    <nav class="navbar navbar-expand-lg explore-navbar navbar-light bg-white">
      <a
        style="font-size: 20px;"
        @click.prevent="$store.commit('setCurrentStep', 'checkout')"
      >
        Reset to Checkout
      </a>

      <div class="container-fluid navbar-container">
        <router-link
          :to="welcomeRoute"
          class="navbar-brand text-primary"
        >
          <img
            :src="logoUrl"
            class="product-logo"
            alt="Rutugo Logo"
          >
        </router-link>

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

          <form v-if="$route.name !== 'booking'" class="form-inline mx-auto">
            <city-input
              v-if="originCity"
              :value="originCity"
              :show-icon="false"
              :disabled="true"
              class="mr-2"
              input-style="font-size: 13px; font-weight: 500; border: 1px solid #e6e6e6;"
              @input="onInputCity($event)"
            />

            <div class="form-group position-relative">
              <input
                :value="datePickerInputText"
                :disabled="true"
                type="text"
                class="form-control search-input-box mr-2"
                placeholder="A week in Month"
                style="font-size: 13px; font-weight: 500; border: 1px solid #e6e6e6;"
                @click.prevent="showDatePicked()"
              >

              <travel-date-picker
                v-if="datePicker"
                v-on-clickaway="onClickOutsideDatePicker"
                :value="query.date"
                @input="onDatePickerInput($event)"
                @hide="hideDatePicker"
              />
            </div>

            <div class="form-group position-relative">
              <input
                :value="travellersInputText"
                :disabled="true"
                type="text"
                class="form-control reduce-width mr-2"
                placeholder="Travellers"
                style="font-size: 13px; font-weight: 500; border: 1px solid #e6e6e6;"
                @click.prevent="showTravellersPicker()"
              >

              <travellers-picker
                v-if="travellersPicker"
                v-on-clickaway="onClickOutsideTravellersPicker"
                :value="query.travellers"
                style="top: 100%;"
                @input="onTravellersInput($event)"
                @hide="hideTravellersPicker()"
              />
            </div>

            <div
              v-if="currencies[currentCurrency]" class="form-group position-relative"
            >
              <div class="dropdown">
                <a
                  class="btn language-selector mr-2"
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
              <div class="dropdown">
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
              <router-link
                :to="exploreRoute"
                class="nav-link notification-area"
              >
                {{ __('Cities') }}
              </router-link>
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
                >{{ notifications.items }}</span>
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
                    <router-link
                      :to="checkoutRoute(plan.id)"
                    >
                      {{ plan.name }}
                    </router-link>
                  </li>
                </ul>
              </div>
            </li>

            <li
              v-if="!user"
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
              v-if="user"
              :class="{active: activeItem === 100}"
              class="nav-item search-input-box user-nav"
            >
              <a
                href="#"
                class="nav-link notification-area"
                @click.prevent="toggleUserDropdown()"
              >
                {{ user.first_name }}

                <span
                  v-if="orders.length"
                  class="notification-circle"
                >{{ orders.length }}</span>
              </a>

              <div
                v-if="userDropdown"
                v-on-clickaway="hideUserDropdown"
                class="dropdown-results"
                style="min-width: 120px; max-width: 120px; right: 0;"
              >
                <ul class="results-list">
                  <li
                    v-for="order in orders"
                    :key="order.id"
                    class="results-list-item"
                    @click="hideUserDropdown()"
                  >
                    <router-link :to="bookingRoute(order.id)">
                      {{ order.plan.name }}
                    </router-link>
                  </li>

                  <li class="results-list-item">
                    <a href="#" @click.prevent="logout()">{{ __('Logout') }}</a>
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
import TravelDatePicker from './travel/TravelDatePicker'
import TravellersPicker from './travellers/TravellersPicker'
import {cloneDeep} from 'lodash'
import LoginModal from './modals/auth/LoginModal'
import RegisterModal from './modals/auth/RegisterModal'
import CityInput from './form/CityInput'

import {LOGO_URL} from '../bootstrap/config'
import axios from 'axios'
import {mixin as clickaway} from 'vue-clickaway'
import * as moment from 'moment'
import {mapGetters} from 'vuex'
import {route} from '../mixins/route'
import {LOGOUT_SUCCESSFULLY} from '../bootstrap/notify-messages'

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
      togglePlans: false,
      userDropdown: false,
      currentLocale: 0,
      toggleLocale: false,
      toggleCurrency: false,
      currentCurrency: 0,
      showLogin: false,
      showRegister: false,
      showResultsDropdown: false,
      searchResults: [],
      datePicker: false,
      travellersPicker: false,
      activeItem: false,
      showNav: false
    }
  },

  computed: {
    ...mapGetters([
      'orders',
      'currentPlanId',
      'plans',
      'query',
      'originCity',
      'user',
      'currencies',
      'locales',
      'notifications'
    ]),

    welcomeRoute () {
      const params = this.currentPlanId
        ? {
          planId: this.currentPlanId
        }
        : {
          //
        }

      return {
        name: 'welcome',
        params,
        query: this.routeQuery
      }
    },

    exploreRoute () {
      return {
        name: 'explore',
        params: {
          planId: this.currentPlanId
        },
        query: this.routeQuery
      }
    },

    hasNotifications () {
      return !!(this.notifications && this.notifications.items)
    },

    isExploreRoute () {
      if (!this.$route) {
        return false
      }

      return this.$route.name === 'explore'
    },

    logoUrl () {
      return LOGO_URL
    },

    travellersInputText () {
      const count = this.countTravellers()

      return count ? count.toString() + ' Travellers' : ''
    },

    datePickerInputText () {
      return moment(this.query.start_date)
        .format('ddd, DD MMM')
          + ' to '
          + moment(this.query.end_date)
            .format('ddd, DD MMM')
    }
  },

  watch: {
    notifications: {
      handler () {
        //
      }
    }
  },

  methods: {
    bookingRoute (orderId) {
      return {
        name: 'booking',
        params: {
          orderId
        },
        query: {
          //
        }
      }
    },

    logout () {
      this.$store.dispatch('logout')

      this.$notification.show(LOGOUT_SUCCESSFULLY)
    },

    checkoutRoute (planId) {
      return {
        name: 'checkout',
        params: {
          planId
        },
        query: this.routeQuery
      }
    },

    onInputCity (value) {
      if (!value) {
        return false
      }

      this.query.origin_city = {
        ...this.query.origin_city,
        id: value
      }

      this.updateQuery()

      this.reloadPage()
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

    reloadPage () {
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
      const count = Object.keys(this.query.themes).length


      return count ? count + ' Themes' : ''
    },

    active (id) {
      this.activeItem = id
    },

    selectPlace (index) {
      this.query.originCity = this.searchResults[index]
      this.showResultsDropdown = false
    },

    updateQuery () {
      this.$store.commit('setQuery', cloneDeep(this.actualQuery))
    },

    onClickOutsideTravellersPicker () {
      this.travellersPicker = false
    },

    onTravellersInput (value) {
      this.query.travellers = {
        ...value
      }

      this.updateQuery()

      this.reloadPage()
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    onDatePickerInput (value) {
      this.query.date = cloneDeep(value)

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
      if (!this.query) {
        return 0
      }

      return this.query.travellers.adults
          + this.query.travellers.children
          + this.query.travellers.infants
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
