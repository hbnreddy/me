<template>
  <div
    class="v-modal"
    tabindex="-1"
    @keydown.esc="hide($event)"
  >
    <div
      class="modal alert-popup"
      style="z-index: 1099"
    >
      <div
        class="modal-dialog"
        role="document"
      >
        <div
          v-on-clickaway="hide"
          class="modal-content"
        >
          <div
            :class="type"
            class="modal-header"
          >
            <i class="fa fa-times-circle-o icon" />
          </div>

          <div
            class="modal-body"
            style="display: block"
          >
            <div class="alert">
              <div class="alert-header">
                <span v-if="type === 'warning'">{{ __('Warning') }}!</span>
                <span v-else-if="type === 'error'">{{ __('Error') }}!</span>
                <span v-else>{{ __('Success') }}!</span>
              </div>

              <slot name="alert" />
            </div>
          </div>

          <div class="modal-footer">
            <button
              class="btn btn-close"
              @click="hide($event)"
            >
              <i class="fa fa-close icon" />
              {{ __('Close') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5; z-index: 1095" />
  </div>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    type: {
      type: String,
      default: () => 'warning'
    }
  },

  methods: {
    hide (evt) {
      evt.stopPropagation()

      this.$emit('hide')
    }
  }
}
</script>
