<template>
  <modal
    class="modal-login"
    @close="close()"
  >
    <h5
      slot="modal-title"
      class="modal-title"
    />

    <div
      slot="modal-body"
      class="modal-body-content"
    >
      <div class="btn btn-block login-with-facebook">
        <i class="fa fa-facebook mr-3" />
        {{ __('Continue with facebook') }}
      </div>

      <div class="btn btn-block login-with-google">
        <i class="fa fa-google mr-3" />
        {{ __('Continue with google') }}
      </div>

      <div class="or text">
&nbsp;
      </div>

      <div class="text">
        <form
          method="post"
          action="/login"
        >
          <input
            :value="csrf()"
            type="hidden"
            name="_token"
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

          <div
            v-if="error"
            class="errors"
            style="color: crimson;"
          >
            {{ error }} <span v-if="waitSeconds">{{ __('Please try again in') }} {{ waitSeconds }} {{ __('seconds') }}.</span>
          </div>

          <div class="form-group custom-control custom-checkbox">
            <input
              id="customCheck2"
              type="checkbox"
              class="custom-control-input"
            >
            <label
              class="custom-control-label"
              for="customCheck2"
            >
              {{ __('Remember me') }}
            </label>
          </div>

          <button
            type="submit"
            class="btn btn-block btn-orange"
          >
            {{ __('Log in') }}
          </button>

          <div class="text-center">
            <a
              href="#/forgot-password"
              class="btn btn-link font-weight-bold text-decoration-none"
            >
              {{ __('Forgot password') }}?
            </a>
          </div>
        </form>
      </div>
    </div>

    <div slot="modal-footer">
      <div class="or">
&nbsp;
      </div>

      <div class="bottom-link">
        <span class="mr-2">{{ __('Don\'t have a rutugo account') }}?</span>
        <a
          href="#"
          @click.prevent="$emit('register')"
        >{{ __('Sign&nbsp;up') }}</a>
      </div>
    </div>
  </modal>
</template>

<script>
import Modal from '../../../components/Modal'
import axios from 'axios'

export default {
  components: {
    Modal
  },

  data () {
    return {
      forgotPassword: true,
      error: false,
      waitSeconds: false,
      user: {
        email: '',
        password: ''
      }
    }
  },

  methods: {
    async submit () {
      await axios
        .post('/login', {
          ...this.user
        })
        .then(r => {
          if (r.data.success) {
            this.error = false
            this.waitSeconds = false

            this.close()
          } else {
            this.user = {
              ...this.user,
              password: ''
            }

            this.error = r.data.message

            if (r.data.wait_seconds) {
              this.waitSeconds = r.data.wait_seconds

              let counterInterval = setInterval(() => {
                if (this.waitSeconds) {
                  this.waitSeconds--
                } else {
                  clearInterval(counterInterval)
                }
              }, 1000)
            }
          }
        })
    },

    close () {
      this.$emit('close')
    },

    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>
