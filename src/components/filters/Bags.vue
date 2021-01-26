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
              :class="{ 'disable' : !max_bags}"
              @click.prevent="decreaseCount()"
            >
              <i class="fa fa-minus" />
            </a>

            <span>{{ max_bags }}</span>

            <a
              :class="{'disable' : max_bags === maxPossible}"
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
      maxPossible: 1,
      filterComponent: false,
      max_bags: 0
    }
  },

  watch: {
    value: {
      handler (val) {
        this.max_bags = val
      },
      immediate: true
    }
  },

  methods: {
    increaseCount () {
      if (this.max_bags === this.maxPossible) {
        return false
      }

      this.$emit('input', this.max_bags + 1)
    },

    decreaseCount () {
      if (this.max_bags === 0) {
        return false
      }

      this.$emit('input', this.max_bags - 1)
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
