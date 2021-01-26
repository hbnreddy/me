<template>
  <div
    class="modal fade show"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    style="display: block; background: #655c5c66;"
  >
    <div
      :class="size ? size : ''"
      class="modal-dialog"
      role="document"
    >
      <div class="modal-content">
        <div class="modal-header">
          <slot name="modal-title" />

          <button
            v-if="showClose"
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
            @click="close()"
          >
            <span>&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <slot name="modal-body" />
        </div>

        <div class="modal-footer">
          <slot name="modal-footer" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    visible: {
      type: Boolean,
      default: false
    },

    size: {
      type: String,
      default: () => ''
    },

    showClose: {
      type: Boolean,
      default: true
    }
  },

  methods: {
    close () {
      this.$emit('close')
    }
  }
}
</script>
