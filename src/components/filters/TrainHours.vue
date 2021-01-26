<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >{{ __('Hours') }}</a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Hours') }}
        </div>

        <div
          class="close"
          @click="closeFilterComponent"
        >
          <i class="fa fa-close" />
        </div>
      </div>

      <div class="body train">
        <div
          v-for="(filter, index) in filters"
          :key="index"
          :class="{'active' : selectedFilters.includes(index)}"
          class="train-filter-item"
          @click="selectTimeFilters(index)"
        >
          {{ filter }}
        </div>

        <div
          class="train-filter-item"
          @click="selectTimeFilters(filters.length)"
        >
          {{ __('Anytime') }}
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

  data () {
    return {
      filterComponent: false,
      filters: [
        '4am',
        '6am',
        '8am',
        '10am',
        '12am',
        '2pm',
        '4pm',
        '6pm',
        '8pm',
        '10pm'
      ],
      selectedFilters: []
    }
  },

  methods: {
    selectTimeFilters (index) {
      if (this.selectedFilters.length >= 2) {
        this.selectedFilters = []
      }

      this.selectedFilters.push(index)
    },

    toggleFilterComponent () {
      this.filterComponent = !this.filterComponent
    },

    closeFilterComponent () {
      this.filterComponent = false
    }
  }
}
</script>
