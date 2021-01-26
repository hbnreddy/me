<template>
  <div
    class="checkout-btn-block"
  >
    <a
      class="btn-checkout btn-order blob blue"
      @click.prevent="payment()"
    >
      {{ __('Proceed for Payment') }}
    </a>

    <payment-modal
      v-if="paymentModal"
      @hide="paymentModal = false"
      @loading="onLoading()"
      @complete="onComplete()"
    />
  </div>
</template>

<script>
import PaymentModal from '../../../views/checkout/payment/PaymentModal'

export default {
  components: {
    PaymentModal
  },

  data () {
    return {
      paymentModal: false
    }
  },

  methods: {
    payment () {
      this.paymentModal = true
    },

    onLoading () {
      this.paymentModal = false
    },

    onComplete (state) {
      this.paymentModal = false

      if (state) {
        console.log('Payment Complete.')
      } else {
        console.log('Payment Failed.')
      }
    }
  }
}
</script>
