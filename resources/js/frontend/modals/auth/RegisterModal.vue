<template>
  <modal
    class="modal-register"
    @close="$emit('close')"
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
        {{ ('Continue with facebook') }}
      </div>

      <div class="btn btn-block login-with-google">
        <i class="fa fa-google mr-3" />
        {{ ('Continue with google') }}
      </div>

      <div class="or text">
&nbsp;
      </div>

      <div
        v-if="!registerForm"
        class="btn btn-block login-with-email"
        @click.prevent="showRegisterForm"
      >
        <i class="fa fa-envelope mr-3" />
        {{ ('Sign up with Email') }}
      </div>

      <div
        v-else
        class="text"
      >
        <form
          method="post"
          action="/register"
        >
          <input
            :value="csrf()"
            type="hidden"
            name="_token"
          >

          <div class="form-group">
            <i class="fa fa-user icon" />

            <input
              id="firstName"
              v-model="user.first_name"
              name="first_name"
              type="text"
              class="form-control"
              aria-describedby="firstName"
              placeholder="First Name"
            >
          </div>

          <div class="form-group">
            <i class="fa fa-user icon" />

            <input
              id="lastName"
              v-model="user.last_name"
              name="last_name"
              type="text"
              class="form-control"
              aria-describedby="lastName"
              placeholder="Last Name"
            >
          </div>

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
              :type="passwordFieldsType"
              name="password"
              class="form-control"
              placeholder="Password"
            >

            <i
              class="fa fa-eye-slash icon show-pass-icon"
              @click.prevent="switchPasswordVisibility"
            />
          </div>

          <div class="form-group custom-control custom-checkbox">
            <input
              id="customCheck1"
              type="checkbox"
              class="custom-control-input"
            >
            <label
              class="custom-control-label"
              for="customCheck1"
            >
              {{ __('I agree all the Terms & Conditions') }}
            </label>
          </div>

          <button
            type="submit"
            class="btn btn-block btn-orange"
          >
            {{ __('Sign up') }}
          </button>
        </form>
      </div>
    </div>

    <div slot="modal-footer">
      <div class="or">
&nbsp;
      </div>

      <div class="bottom-link">
        {{ __('Already have a Rutugo account') }}?
        <a
          href="#"
          class="mx-2"
          @click.prevent="$emit('login')"
        >
          {{ __('Log in') }}
        </a>
      </div>
    </div>
  </modal>
</template>

<script>
import Modal from '../../../components/Modal'

export default {
  components: {
    Modal
  },

  data () {
    return {
      registerForm: false,
      passwordFieldsType: 'password',
      user: {
        first_name: '',
        last_name: '',
        email: '',
        password: ''
      }
    }
  },

  methods: {
    showRegisterForm () {
      this.registerForm = true
    },

    switchPasswordVisibility () {
      this.passwordFieldsType = this.passwordFieldsType === 'password' ? 'text' : 'password'
    },

    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>
