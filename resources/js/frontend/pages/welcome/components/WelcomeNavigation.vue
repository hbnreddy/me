<template>
  <nav
    id="navBar"
    class="navbar navbar-expand-md context-video nav-bar-backcolor"
    :class="[
      {'scrolled-navbar': scrolledView},
      scrolledView ? 'navbar-light' : 'navbar-dark'
    ]"
  >
    <div class="container-fluid">
      <a
        class="navbar-brand"
        href="/"
      >
        <img
          v-if="scrolledView"
          src="/logo/logo-blue.png"
          class="product-logo"
          alt="Rutugo Logo"
        >
        <img
          v-else
          src="/logo/logo-white.png"
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
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a
              v-if="!scrolledView"
              v-smooth-scroll="{ duration: 1000, offset: -80 }"
              :class="{'nav-items-foregroundcolor1': scrolledView}"
              class="nav-link"
              href="#how-it-works"
            >
              {{ __('How it works?') }}
            </a>
          </li>
        </ul>

        <ul class="navbar-nav ml-auto">
          <li
            v-if="locales[currentLocale]"
            :class="{active: activeItem === 1}"
            class="nav-item search-input-box user-nav"
          >
            <a
              id="navItemLinksLang"
              href="#"
              class="nav-link notification-area"
              :class="{'nav-items-foregroundcolor1': scrolledView}"
              @click.prevent="toggleLocale = true"
            >
              <i class="fa fa-globe mr-1" />
              {{ locales[currentLocale].code.toUpperCase() }}
            </a>

            <div
              v-if="toggleLocale"
              v-on-clickaway="hideLocalePicker"
              class="dropdown-results"
              style="min-width: 120px; max-width: 120px; right: 0;"
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
          </li>

          <li
            :class="{active: activeItem === 1}"
            class="nav-item user-nav"
          >
            <a
              id="navItemLinksPlans"
              :href="bookingRoute"
              class="nav-link notification-area"
              :class="{'nav-items-foregroundcolor1': scrolledView}"
            >
              {{ __('My Plans') }}

              <span
                v-if="hasNotifications"
                class="notification-circle"
              >{{ notifications.count }}</span>
            </a>
          </li>

          <li
            v-if="authenticated"
            :class="{active: activeItem === 100}"
            class="nav-item search-input-box user-nav"
          >
            <a
              id="navItemLinksName"
              href="#"
              class="nav-link notification-area"
              :class="{'nav-items-foregroundcolor1': scrolledView}"
              @click.prevent="toggleUserDropdown()"
            >
              {{ authUser.first_name }}

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
                  v-for="(order, index) in orders"
                  :key="index"
                  class="results-list-item"
                >
                  <a href="#">
                    {{ order.name }}
                  </a>
                </li>

                <li class="results-list-item">
                  <a href="/logout">Logout</a>
                </li>
              </ul>
            </div>
          </li>

          <li
            v-if="!authenticated"
            class="nav-item"
          >
            <a
              id="navItemLinksLogin"
              href="#"
              class="nav-link"
              :class="{'nav-items-foregroundcolor1': scrolledView}"
              @click.prevent="showLogin = true"
            >{{ __('Login') }}</a>
          </li>
        </ul>
      </div>
    </div>

    <login-modal
      v-if="showLogin"
      @register="onRegister()"
      @close="setLoginView(false)"
    />

    <register-modal
      v-if="showRegister"
      @login="onLogin()"
      @close="setRegisterView(false)"
    />
  </nav>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'

import LoginModal from '../../../modals/auth/LoginModal'
import RegisterModal from '../../../modals/auth/RegisterModal'
import * as moment from 'moment'
import axios from 'axios'

export default {
  components: {
    LoginModal,
    RegisterModal
  },

  mixins: [
    clickaway
  ],

  props: {
    authUser: {
      type: Object | Boolean,
      default: () => false
    },

    query: {
      type: Object | Boolean,
      required: true
    },

    notifications: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      toggleLocale: false,
      locales: [],
      currentLocale: false,
      orders: [],
      userDropdown: false,
      showLogin: false,
      showRegister: false,
      activeItem: false,
      showNav: false,
      scrolledView: false
    }
  },

  computed: {
    hasNotifications () {
      return this.notifications && this.notifications.count
    },

    authenticated () {
      return this.authUser !== false
    },

    bookingRoute () {
      if (!this.query) {
        return '/'
      }

      const querystring = require('querystring')
      const date = this.query.date

      let data = {
        currency: this.query.currency.code,
        travellers: Object.values(this.query.travellers).join('-'),
        origin_city_id: this.query.origin_city.id,
        d_type: date.type
      }

      if (date.type === 'exact') {
        data = {
          ...data,
          d_start: moment(date.start).format(this.$const('BASE_DATE_FORMAT')),
          d_end: moment(date.end).format(this.$const('BASE_DATE_FORMAT'))
        }
      } else {
        data = {
          ...data,
          flex_type: date.flexible_type,
          d_month: date.month,
          year: date.year
        }
      }

      return `/booking?` + querystring.stringify(data)
    }
  },

  created () {
    if (this.authenticated) {
      this.fetchOrders()
    }
    window.addEventListener('scroll', this.handleScroll)
    this.fetchLocales()
  },

  destroyed () {
    window.removeEventListener('scroll', this.handleScroll)
  },

  methods: {
    setLocale (index) {
      this.currentLocale = index
      this.toggleLocale = false

      this.$redirect('/',
        {
          //
        },
        false,
        this.locales[this.currentLocale].code
      )
    },

    handleScroll (event) {
      this.scrolledView = window.scrollY > 10
    },

    hideLocalePicker () {
      this.toggleLocale = false
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

    async fetchOrders () {
      await axios
        .get(`/api/orders`)
        .then(r => {
          if (r.data.success) {
            this.orders = r.data.entities
          }
        })
    },

    active (id) {
      this.activeItem = id
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

    resolveConstraints (constraints) {
      if (constraints.includes('auth')) {
        return this.authUser !== false
      } else if (constraints.includes('not_auth')) {
        return this.authUser === false
      }

      return true
    },

    onRegister () {
      this.showLogin = false
      this.showRegister = true
    },

    onLogin () {
      this.showLogin = true
      this.showRegister = false
    },

    setLoginView (state) {
      this.showLogin = state
    },

    setRegisterView (state) {
      this.showRegister = state
    },

    toggleNavbar () {
      this.showNav = !this.showNav
    }
  }
}
</script>
