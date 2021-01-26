<template>
  <div
    class="v-modal"
    tabindex="-1"
    @keydown.esc="hide($event)"
  >
    <div
      class="modal payment-modal"
      style="z-index: 2001"
    >
      <div
        class="modal-dialog modal-dialog-centered modal-lg"
        role="document"
      >
        <div
          class="modal-content"
        >
          <div
            class="modal-body checkout-page"
            style="display: block"
          >
            <div class="tab-content">
              <card-tab-content
                @hide="hide($event)"
                @submit="submitPayment($event)"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5; z-index: 2000" />
  </div>
</template>

<script>
import CardTabContent from './CardInfoTabContent'

import {mapGetters} from 'vuex'

export default {
  components: {
    CardTabContent
  },

  computed: {
    ...mapGetters([
      'currentPlan',
      'steps'
    ])
  },

  methods: {
    hide () {
      this.$emit('hide')
    },

    async submitPayment (cardholder) {
      this.$emit('loading')

      const response = await this.$store.dispatch('checkoutPayment', cardholder)

      this.$notification.show({
        type: response.success ? 'success' : 'error',
        title: 'Payment Processing',
        text: response.message
      })

      if (!response.success) {
        await this.$store.dispatch('setCurrentStep', 'checkout')
        this.$emit('complete', false)

        return false
      }

      const entities = response.entities

      for (let i = 0; i < entities.length; ++i) {
        if (['travel', 'hotel'].includes(entities[i].type)) {
          await this.$store.dispatch('refreshItem', entities[i])
        }
      }

      await this.$store.dispatch('setCurrentStep', 'ready-for-booking')

      this.$emit('complete', true)
    }
  }
}
</script>
