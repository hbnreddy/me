<template>
  <div
    v-if="loading"
    :class="{ 'loading-small': small === true }"
    class="loading loading-block"
    style="text-align: center; padding: 16px 0;"
  >
    <loading
      :active.sync="loading"
      :is-full-page="fullPage"
      :color="color"
    />

    <p v-if="text">
      {{ text }}
    </p>
  </div>
</template>

<script>
import Loading from 'vue-loading-overlay'

export default {
  components: {
    Loading
  },

  props: {
    text: {
      type: String,
      default: () => ''
    },

    color: {
      type: String,
      default: () => '#fd753b'
    },

    fullPage: {
      type: Boolean,
      default: false
    },

    loading: {
      type: Boolean,
      default: false
    },

    small: {
      type: Boolean,
      default: false
    }
  }
}
</script>
