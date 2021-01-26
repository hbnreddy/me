<template>
  <modal
    class="modal-login"
    @close="close()"
  >
    <h5
      slot="modal-title"
      class="modal-title"
    >
      {{ __('Reset your password') }}
    </h5>

    <div
      slot="modal-body"
      class="modal-body-content"
    >
      <div class="text">
        <form
          method="post"
          action="/reset-password"
        >
          <input
            :value="csrf()"
            type="hidden"
            name="_token"
          >

          <input
            :value="token"
            type="hidden"
            name="token"
          >

          <div class="form-group">
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

          <div class="form-group">
            <i class="fa fa-lock icon" />

            <input
              id="password"
              v-model="user.password"
              name="password"
              type="password"
              class="form-control"
              placeholder="Password"
            >
          </div>

          <div class="form-group">
            <i class="fa fa-lock icon" />

            <input
              id="password_confirmation"
              v-model="user.password_confirmation"
              name="password_confirmation"
              type="password"
              class="form-control"
              placeholder="Confirm password"
            >
          </div>

          <div
            v-if="error"
            class="errors"
            style="color: crimson;"
          >
            {{ error }} <span v-if="waitSeconds">{{ __('Please try again in') }} {{ waitSeconds }} {{ __('seconds') }}.</span>
          </div>

          <button
            type="submit"
            class="btn btn-block btn-orange"
          >
            {{ __('Reset') }}
          </button>
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
      error: false,
      waitSeconds: false,
      user: {
        email: '',
        password: '',
        password_confirmation: ''
      },
      token: ''
    }
  },

  created () {
    this.token = this.$route.query.token
  },

  methods: {
    async submit () {
      if (!this.validate()) {
        return false
      }

      await axios
        .post('/reset-password/' + this.token, {
          ...this.user,
          token: this.token
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

      if (this.emptyField(this.user.email) ||
          this.emptyField(this.user.password)
      ) {
        this.error = 'All fields are required.'

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
