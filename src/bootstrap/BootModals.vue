<template>
  <div class="boot-modals">
    <login-modal
      v-if="showLogin"
      @close="showLogin = false"
    />

    <register-modal
      v-if="showRegister"
      @close="showRegister = false"
    />

    <reset-password-modal
      v-if="showResetPassword"
      @close="showResetPassword = false"
    />

    <forgot-password-modal
      v-if="showForgotPassword"
      @close="showPasswordChanged = false"
    />

    <password-changed-modal v-if="showPasswordChanged" />
  </div>
</template>

<script>
import PasswordChangedModal from '../components/modals/auth/PasswordChangedModal'
import ForgotPasswordModal from '../components/modals/auth/ForgotPasswordModal'
import ResetPasswordModal from '../components/modals/auth/ResetPasswordModal'
import LoginModal from '../components/modals/auth/LoginModal'
import RegisterModal from '../components/modals/auth/RegisterModal'

export default {
  components: {
    PasswordChangedModal,
    ForgotPasswordModal,
    RegisterModal,
    ResetPasswordModal,
    LoginModal
  },

  data () {
    return {
      showLogin: false,
      showRegister: false,
      showPasswordChanged: false,
      showResetPassword: false,
      showForgotPassword: false
    }
  }
}
</script>
