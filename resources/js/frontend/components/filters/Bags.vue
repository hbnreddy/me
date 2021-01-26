<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      {{ __('Bags') }}
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Bags') }}
        </div>

        <div
          class="close"
          @click="closeFilterComponent"
        >
          <i class="fa fa-close" />
        </div>
      </div>

      <div class="body">
        <div class="d-flex justify-content-between align-items-center">
          <div>{{ __('Hand bags') }}</div>

          <div class="actions">
            <a
              :class="{ 'disable' : !bags}"
              @click.prevent="decreaseCount()"
            >
              <i class="fa fa-minus" />
            </a>

            <span>{{ bags }}</span>

            <a
              :class="{'disable' : bags === 1}"
              @click.prevent="increaseCount()"
            >
              <i class="fa fa-plus" />
            </a>
          </div>
        </div>
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
      type: Number,
      default: 0
    }
  },

  data () {
    return {
      filterComponent: false,
      bags: 0
    }
  },

  watch: {
    value: {
      handler (val) {
        this.bags = val
      },
      immediate: true
    }
  },

  methods: {
    increaseCount () {
      this.$emit('input', this.bags + 1)
    },

    decreaseCount () {
      this.$emit('input', this.bags - 1)
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
