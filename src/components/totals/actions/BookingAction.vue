<template>
  <div
    class="checkout-btn-block"
  >
    <a
      class="btn-checkout"
      @click.prevent="booking()"
    >
      {{ __('Proceed for Booking') }}
    </a>
  </div>
</template>

<script>
export default {
  methods: {
    async booking () {
      const status = await this.$store.dispatch('makeOrder')

      if (!status.success) {
        this.$notification.show({
          type: 'warning',
          title: 'Warning!',
          text: status.message
        })

        return false
      }

      await this.$store.dispatch('resetCart')

      this.$notification.show({
        type: 'success',
        title: 'Well done!',
        text: status.message
      })

      setTimeout(() => {
        this.$router.push({
          name: 'booking',
          params: {
            orderId: status.order_id
          },
          query: {
            //
          }
        })
      }, 2000)
    }
  }
}
</script>
