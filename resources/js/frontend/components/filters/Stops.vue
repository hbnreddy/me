<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      {{ __('Stops') }}
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Stops') }}
        </div>

        <div
          class="close"
          @click="closeFilterComponent"
        >
          <i class="fa fa-close" />
        </div>
      </div>

      <div class="body stops">
        <label
          class="radio-container mr-4"
        >
          {{ __('As many stops') }}

          <input
            :checked="value === false"
            type="radio"
            name="stops"
            @change="onChange(false)"
          >
          <span class="checkmark" />
        </label>

        <label
          class="radio-container mr-4"
        >
          {{ __('No stops') }}

          <input
            :checked="value === 0"
            type="radio"
            name="stops"
            @change="onChange(0)"
          >
          <span class="checkmark" />
        </label>

        <label
          class="radio-container mr-4"
        >
          {{ __('At most one stop') }}

          <input
            :checked="value === 1"
            type="radio"
            name="stops"
            @change="onChange(1)"
          >
          <span class="checkmark" />
        </label>

        <label
          class="radio-container mr-4"
        >
          {{ __('At most 2 stops') }}

          <input
            :checked="value === 2"
            type="radio"
            name="stops"
            @change="onChange(2)"
          >
          <span class="checkmark" />
        </label>
      </div>
    </div>
  </div>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Number | Boolean,
      default: false
    }
  },

  data () {
    return {
      filterComponent: false
    }
  },

  methods: {
    onChange (value) {
      this.$emit('input', value)
    },

    toggleFilterComponent () {
      this.filterComponent = !this.filterComponent
    },

    closeFilterComponent () {
      if (this.filterComponent) {
        this.filterComponent = false

        this.$emit('reload')
      }
    }
  }
}
</script>
