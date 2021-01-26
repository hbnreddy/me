<template>
  <div class="d-flex justify-content-center align-items-center">
    <div
      v-if="showText"
      class="toggle-status"
    >
      {{ leftText !== '' ? leftText : __('Disabled') }}
    </div>

    <label class="switch">
      <input
        v-model="toggle"
        type="checkbox"
        @change="input"
      >
      <span class="slider round" />
    </label>

    <div
      v-if="showText"
      class="toggle-status"
    >
      {{ rightText !== '' ? rightText : 'Enabled' }}
    </div>
  </div>
</template>

<script>

import {DISABLED, ENABLED} from '../admin/states'

export default {
  props: {
    enabled: {
      type: Boolean | Number,
      default: false
    },

    showText: {
      type: Boolean,
      default: false
    },

    leftText: {
      type: String,
      default: () => ''
    },

    rightText: {
      type: String,
      default: () => ''
    }
  },

  data () {
    return {
      toggle: true
    }
  },

  watch: {
    enabled: {
      handler (val) {
        this.toggle = val
      },
      immediate: true
    }
  },

  methods: {
    input () {
      this.$emit('input', this.toggle ? ENABLED : DISABLED)
    }
  }
}
</script>
