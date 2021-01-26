<template>
  <div
    class="checkout-btn-block delete-btn"
  >
    <a
      class="btn-checkout btn-delete"
      @click.prevent="showDeleteConfirmation = true"
    >
      {{ __('Delete Plan') }}
    </a>

    <confirm-popup v-if="showDeleteConfirmation" @confirm="onConfirmDelete($event)">
      <p slot="text">
        {{ __('Are you sure you want to delete your plan?') }}
      </p>
    </confirm-popup>
  </div>
</template>

<script>
import ConfirmPopup from '../../modals/confirmation/ConfirmPopup'

import {PLAN_DELETED_SUCCESSFULLY} from '../../../bootstrap/notify-messages'

export default {
  components: {
    ConfirmPopup
  },

  data () {
    return {
      showDeleteConfirmation: false
    }
  },

  async onConfirmDelete (state) {
    this.showDeleteConfirmation = false

    if (state) {
      await this.deletePlan(true)

      this.$notification.show(PLAN_DELETED_SUCCESSFULLY)
    }
  },

  async deletePlan (redirect = false) {
    await this.$store.dispatch('resetCart')

    if (redirect) {
      setTimeout(() => {
        this.$router.push({
          name: 'welcome',
          params: {
            //
          },
          query: {
            //
          }
        })
      }, 1000)
    }
  }
}
</script>
