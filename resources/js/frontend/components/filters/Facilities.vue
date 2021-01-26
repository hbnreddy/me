<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >{{ __('Facilities') }}</a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Facilities') }}
        </div>

        <div class="small text-muted">
          2 {{ __('locations') }}
        </div>
      </div>

      <div class="body facilities">
        <div
          v-for="(facility, index) in facilities"
          :key="index"
          :class="{'active' : selectedFacilities.includes(index)}"
          class="text-center facility"
          @click="selectFacilities(index)"
        >
          <div>
            <i
              :class="'fa-' + facility.icon"
              class="fa icon"
            />
          </div>
          <div>{{ facility.name }}</div>
        </div>
      </div>

      <div class="buttons text-right p-2">
        <a
          href="#"
          class="mx-2 text-dark text-decoration-none"
          @click.prevent="closeFilterComponent()"
        >{{ __('Cancel') }}</a>
        <a
          href="#"
          class="mx-2 text-primary text-decoration-none"
          @click.prevent="applyFilter()"
        >{{ __('Apply') }}</a>
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
      selectedFacilities: [],
      facilities: [
        {
          name: 'Free Wi-fi',
          icon: 'wifi'
        },
        {
          name: 'Fitness Center',
          icon: 'coffee'
        },
        {
          name: 'Free breakfast',
          icon: 'coffee'
        },
        {
          name: 'Suitable for children',
          icon: 'man'
        },
        {
          name: 'Free parking',
          icon: 'paypal'
        },
        {
          name: 'Pool',
          icon: 'bath'
        }
      ]
    }
  },

  methods: {
    onChange () {

    },

    applyFilter () {
      this.filterComponent = false
    },

    toggleFilterComponent () {
      this.filterComponent = !this.filterComponent
    },

    closeFilterComponent () {
      this.filterComponent = false
    },

    selectFacilities (index) {
      if (this.selectedFacilities.includes(index)) {
        this.selectedFacilities.splice(this.selectedFacilities.indexOf(index), 1)
      } else {
        this.selectedFacilities.push(index)
      }
    }
  }
}
</script>
