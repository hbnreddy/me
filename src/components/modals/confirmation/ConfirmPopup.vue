<template>
  <div
    class="v-modal"
    tabindex="-1"
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
          class="modal-content"
        >
          <div
            class="modal-header"
          >
          </div>

          <div
            class="modal-body"
            style="display: block"
          >
            <div class="alert">
              <div class="alert-header">
                Please confirm this operation
              </div>

              <slot name="text" />
            </div>
          </div>

          <div class="modal-footer">
            <button
              class="btn btn-close"
              @click="confirm($event, false)"
            >
              <i class="fa fa-close icon" />
              {{ __('Cancel') }}
            </button>

            <button
              class="btn btn-close"
              style="background: red;"
              @click="confirm($event, true)"
            >
              <i class="fa fa-close icon" />
              {{ __('Delete') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5; z-index: 1095" />
  </div>
</template>

<script>
export default {
  methods: {
    confirm (event, state) {
      event.stopPropagation()

      this.$emit('confirm', state)
    }
  }
}
</script>
