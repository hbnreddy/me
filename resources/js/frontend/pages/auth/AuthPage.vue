<template>
  <div class="auth-page">
    <login-modal
      v-if="showLogin"
      @register="onRegister()"
    />

    <register-modal
      v-if="showRegister"
      @login="onLogin()"
    />
  </div>
</template>

<script>
import LoginModal from '../../modals/auth/LoginModal'
import RegisterModal from '../../modals/auth/RegisterModal'

export default {
  components: {
    RegisterModal,
    LoginModal
  },

  data () {
    return {
      showLogin: true,
      showRegister: false
    }
  },

  methods: {
    onRegister () {
      this.showRegister = true
      this.showLogin = false
    },

    onLogin () {
      this.showRegister = false
      this.showLogin = true
    }
  }
}
</script>
