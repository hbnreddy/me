<template>
  <nav
    id="navBar"
    class="navbar navbar-expand-md context-video nav-bar-backcolor position-fixed"
    :class="[
      {'scrolled-navbar': scrolledView},
      scrolledView ? 'navbar-light' : 'navbar-dark'
    ]"
  >
    <div class="container-fluid">
      <a
        class="navbar-brand"
        href="#"
      >
        <img
          v-if="scrolledView"
          src="../../../assets/logo/logo-blue.png"
          class="product-logo"
          alt="Rutugo Logo"
        >
        <img
          v-else
          src="../../../assets/logo/logo-white.png"
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
            @click.prevent="togglePlans = !togglePlans"
          >
            <a
              id="navItemLinksPlans"
              :href="'/'"
              class="nav-link notification-area"
              :class="{'nav-items-foregroundcolor1': scrolledView}"
            >
              {{ __('My Plans') }}

              <span
                v-if="hasNotifications"
                class="notification-circle"
              >{{ notifications.items }}</span>
            </a>

            <div
              v-if="togglePlans"
              v-on-clickaway="hidePlanPicker"
              class="dropdown-results"
              style="min-width: 120px; max-width: 120px; top: 60px;"
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
            v-if="user"
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
                  v-for="(order, index) in orders"
                  :key="index"
                  class="results-list-item"
                  @click.prevent="userDropdown = false"
                >
                  <router-link :to="bookingRoute(order.id)">
                    {{ order.plan.name }}
                  </router-link>
                </li>

                <li class="results-list-item">
                  <a href="/logout">Logout</a>
                </li>
              </ul>
            </div>
          </li>

          <li
            v-if="!user"
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
import LoginModal from '../../../components/modals/auth/LoginModal'
import RegisterModal from '../../../components/modals/auth/RegisterModal'

import {mixin as clickaway} from 'vue-clickaway'
import { mapGetters } from 'vuex'


export default {
  components: {
    LoginModal,
    RegisterModal
  },

  mixins: [
    clickaway
  ],

  data () {
    return {
      togglePlans: false,
      toggleLocale: false,
      currentLocale: false,
      userDropdown: false,
      showLogin: false,
      showRegister: false,
      activeItem: false,
      showNav: false,
      scrolledView: false
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId',
      'query',
      'user',
      'notifications',
      'plans',
      'orders',
      'locales'
    ]),

    hasNotifications () {
      return this.notifications && this.notifications.items
    }
  },

  created () {
    this.currentLocale = this.locales.findIndex(e => {
      return e.code.toLowerCase() === window._locale.toLowerCase()
    })

    window.addEventListener('scroll', this.handleScroll)
  },

  destroyed () {
    window.removeEventListener('scroll', this.handleScroll)
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

    checkoutRoute (planId) {
      const query = this.query ? {
        ...this.query,
        themes: this.query.themes.join(','),
        travellers: Object.values(this.query.travellers).join(',')
      } : {
        //
      }

      return {
        name: 'checkout',
        params: {
          planId
        },
        query
      }
    },

    hidePlanPicker () {
      this.togglePlans = false
    },

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

    handleScroll () {
      this.scrolledView = window.scrollY > 10
    },

    hideLocalePicker () {
      this.toggleLocale = false
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
        return this.user !== false
      } else if (constraints.includes('not_auth')) {
        return this.user === false
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
