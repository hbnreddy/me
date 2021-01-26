<template>
  <div class="checkout-page">
    <div class="wrapper">
      <div class="box">
        <div v-if="success">
          <h5>{{ __('Your order has been processed and created') }}.</h5>
        </div>

        <div v-else>
          <h5>{{ __('Proceed Checkout and Create an Order') }}</h5>

          <p class="text">
            {{ __('Please Submit by pushing the button bellow') }}.
          </p>

          <div class="input-group mb-3">
            <div class="input-group d-flex align-items-center">
              <input
                v-model="agree" type="checkbox"
                class="mr-2"
              >

              <div>
                {{ __('I read and agree with all') }} <a href="#">{{ __('Terms & Conditions') }}</a>.
              </div>

              <small v-if="error" style="color: crimson; display: block; width: 100%;">
                {{ error }}
              </small>
            </div>
          </div>

          <a
            href="#"
            class="btn btn-orange"
            @click.prevent="proceed()"
          >
            <div class="d-flex align-items-center">
              <loading-circle
                :loading="loading"
                :small="true"
                :color="'#ffffff'"
                class="mr-3"
              />

              <div>
                {{ __('Proceed') }}
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingCircle from '../../../../components/LoadingCircle'

import axios from 'axios'
import { route } from '../../../mixins/route'

export default {
  components: {
    LoadingCircle
  },

  mixins: [
    route
  ],

  data () {
    return {
      agree: false,
      error: false,
      loading: false,
      success: false
    }
  },

  methods: {
    async proceed () {
      this.loading = true

      this.error = false

      if (!this.agree) {
        this.error = 'Please agree to Terms and Conditions to place the order.'
        this.loading = false

        return false
      }

      await axios
        .post(`/api/cart/order`)
        .then(r => {
          if (r.data.success) {
            if (r.data.success) {
              this.$redirect('explore', this.routeParams)
            }

            if (r.data.message) {
              this.$notification.show(r.data.message)
            }
          }
        })

      this.loading = false
    }
  }
}
</script>
