<template>
  <div class="application">
    <loading
      :active.sync="loading"
      :is-full-page="true"
      class="loading-circle"
    />

    <navbar />

    <router-view />

    <footer-component />

    <login-modal
      v-if="showLogin"
      @close="closeLogin()"
    />

    <reset-password-modal
      v-if="showResetPassword"
      @close="closeResetPassword()"
    />

    <forgot-password-modal
      v-if="showForgotPassword"
      @close="closeForgotPassword()"
    />

    <password-changed-modal v-if="showPasswordChanged" />
  </div>
</template>

<script>
import Navbar from '../pages/welcome/components/WelcomeNavigation'
import FooterComponent from './Footer'
import {mapGetters} from 'vuex'
import Loading from 'vue-loading-overlay'
import LoginModal from '../modals/auth/LoginModal'
import ResetPasswordModal from '../modals/auth/ResetPasswordModal'
import ForgotPasswordModal from '../modals/auth/ForgotPasswordModal'
import PasswordChangedModal from '../modals/auth/PasswordChangedModal'

export default {

  components: {
    PasswordChangedModal,
    ForgotPasswordModal,
    ResetPasswordModal,
    LoginModal,
    Navbar,
    Loading,
    FooterComponent
  },
  props: {
    data: {
      type: Object,
      default: () => {
      }
    }
  },

  data () {
    return {
      showLogin: false,
      showRegister: false,
      showPasswordChanged: false,
      showResetPassword: false,
      showForgotPassword: false,
      currentRoute: null
    }
  },

  computed: {
    ...mapGetters([
      'loading',
      'authUser'
    ])
  },

  watch: {
    $route: {
      handler (route) {
        if (route.hash.includes('login')) {
          this.showLogin = true
          this.showForgotPassword = false
        }

        if (route.hash.includes('reset-password')) {
          this.showResetPassword = true
        }

        if (route.hash.includes('forgot-password')) {
          this.showLogin = false
          this.showForgotPassword = true
        }

        if (route.hash.includes('password-changed')) {
          this.showPasswordChanged = true

          setTimeout(() => {
            this.showPasswordChanged = false
          }, 2000)
        }

        this.currentRoute = route.name
      },
      immediate: true
    }
  },

  created () {
    if (!this.authUser && this.data.user) {
      this.$store.commit('setAuthUser', this.data.user)
    }

    this.$store.commit('setThemes', this.data.themes)
    this.$store.commit('setCities', this.data.cities)
    this.$store.commit('setQuery', this.data.query)
  },

  methods: {
    closeLogin () {
      this.$router.push('')

      this.showLogin = false
    },

    closeResetPassword () {
      this.$router.push('')

      this.showResetPassword = false
    },

    closeForgotPassword () {
      this.$router.push('')

      this.showForgotPassword = false
    }
  }
}
</script>
