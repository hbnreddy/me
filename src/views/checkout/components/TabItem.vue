<template>
  <li class="nav-item cursor-pointer position-relative">
    <a
      :class="{ active }"
      class="nav-link"
      role="tab"
      @click.prevent="$emit('select', tab.key)"
    >
      {{ tab.text }}
    </a>

    <img
      v-if="loading"
      src="/loading-circles.gif"
      class="icon-loading"
      alt="..."
    >

    <i
      v-else
      :class="{ active, complete: isComplete, 'blob circle blob-error errors': hasErrors }"
      class="icon-check fa fa-check-circle"
    />
  </li>
</template>

<script>

import { mapGetters } from 'vuex'

export default {
  props: {
    tab: {
      type: Object,
      required: true
    },

    active: {
      type: Boolean,
      default: false
    },

    complete: {
      type: Boolean,
      default: false
    },

    errors: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      loading: false
    }
  },

  computed: {
    ...mapGetters([
      'steps'
    ]),

    isComplete () {
      return this.steps[this.tab.key].complete
    },

    hasErrors () {
      return this.steps[this.tab.key].errors.length
    }
  },

  mounted () {
    this.$eventHub.on(`${this.tab.key}.loading`, this.onLoading)
  },

  beforeDestroy () {
    this.$eventHub.off(`${this.tab.key}.loading`, this.onLoading)
  },

  methods: {
    onLoading (state) {
      this.loading = state
    }
  }
}
</script>
