<template>
  <modal
    class="modal-login"
    @close="close()"
  >
    <h5
      slot="modal-title"
      class="modal-title"
    >
      <span v-if="!confirmed">{{ __('Reset your password') }}</span>
    </h5>

    <div
      slot="modal-body"
      class="modal-body-content"
    >
      <div class="text">
        <form
          method="post"
          @submit.prevent="submit()"
        >
          <input
            :value="csrf()"
            type="hidden"
            name="_token"
          >

          <h6 v-if="confirmed">
            {{ __('Please verify your e-mail to reset your password') }}.
          </h6>

          <div
            v-if="!confirmed"
            class="form-group"
          >
            <i class="fa fa-envelope icon" />

            <input
              id="emailAddress"
              v-model="user.email"
              name="email"
              type="email"
              class="form-control"
              aria-describedby="emailAddress"
              placeholder="Email address"
            >
          </div>

          <div
            v-if="error"
            class="errors"
            style="color: crimson;"
          >
            {{ error }}
          </div>

          <button
            v-if="!confirmed"
            type="submit"
            class="btn btn-block btn-orange"
          >
            {{ __('Submit') }}
          </button>

          <div class="text-center">
            <a
              href="#/login"
              class="btn btn-link font-weight-bold text-decoration-none"
            >
              {{ __('Back to login') }}
            </a>
          </div>
        </form>
      </div>
    </div>

    <div slot="modal-footer" />
  </modal>
</template>

<script>
import Modal from '../Modal'
import axios from 'axios'

export default {
  components: {
    Modal
  },

  data () {
    return {
      confirmed: false,
      error: false,
      user: {
        email: ''
      }
    }
  },

  methods: {
    async submit () {
      if (!this.validate()) {
        return false
      }

      await axios
        .post('/reset-password-email', {
          ...this.user
        })
        .then(r => {
          if (r.data.success) {
            this.confirmed = true
          } else {
            this.error = r.data.message
          }
        })
    },

    close () {
      this.$emit('close')
    },

    emptyField (value) {
      return value === ''
    },

    validate () {
      if (this.user.password !== this.user.password_confirmation) {
        this.error = 'Passwords do not match.'

        return false
      }

      if (this.emptyField(this.user.email)) {
        this.error = 'E-mail field is required.'

        return false
      }

      return true
    },

    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>
