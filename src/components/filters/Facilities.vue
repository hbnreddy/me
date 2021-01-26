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
      </div>

      <div class="body facilities">
        <div
          v-for="(facility, index) in facilities"
          :key="index"
          :class="{'active' : selected.includes(facility.type)}"
          class="text-center facility"
          @click="toggleFacility(facility.type)"
        >
          <div>
            <i
              :class="'fa-' + facility.icon"
              class="fa icon"
            />
          </div>
          <div>{{ printText(facility.type) }}</div>
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
import {mapGetters} from 'vuex'

export default {
  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      filterComponent: false,
      selected: []
    }
  },

  computed: {
    ...mapGetters([
      'facilities'
    ])
  },

  watch: {
    value: {
      handler () {
        this.selected = [...this.value]
      },
      immediate: true
    }
  },

  methods: {
    printText (facility) {
      return facility
        .toLowerCase()
        .split('_')
        .map(e => e[0].toUpperCase() + e.substr(1, e.length - 1))
        .join(' ')
    },

    applyFilter () {
      this.filterComponent = false

      this.$emit('input', [...this.selected])
    },

    toggleFilterComponent () {
      this.filterComponent = !this.filterComponent
    },

    closeFilterComponent () {
      this.filterComponent = false
    },

    toggleFacility (type) {
      if (this.selected.includes(type)) {
        this.selected.splice(this.selected.indexOf(type), 1)
      } else {
        this.selected.push(type)
      }
    }
  }
}
</script>
