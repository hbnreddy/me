<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      {{ __('Airlines') }}
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Airlines') }}
        </div>

        <div
          class="close"
          @click="closeFilterComponent"
        >
          <i class="fa fa-close" />
        </div>
      </div>

      <div class="body">
        <div class="d-flex justify-content-between mb-3">
          <div>{{ __('Select all airlines') }}</div>

          <toggle-input
            :enabled="!value.length"
          />
        </div>

        <div>
          <div class="text-capitalize type">
            Airlines
          </div>

          <div
            v-for="(airline, airlineIndex) in airlines"
            :key="airlineIndex"
          >
            <div class="form-group custom-control custom-checkbox">
              <input
                :id="'a-' + airlineIndex"
                type="checkbox"
                class="custom-control-input"
                @change="onChange(airline.id)"
              >

              <label
                :for="'a-' + airlineIndex"
                class="custom-control-label"
              >
                {{ airline.name }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'
import ToggleInput from '../../../components/ToggleInput'

export default {
  components: {
    ToggleInput
  },

  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Array,
      default: () => []
    },

    airlines: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      filterComponent: false
    }
  },

  watch: {
    value: {
      handler () {
        //
      },
      immediate: true
    }
  },

  methods: {
    onChange (id) {
      // TODO: Fix this.

      // const values = [
      //   ...this.value
      // ]
      //
      // if (!values.includes(id)) {
      //   values.push(id)
      // } else {
      //   const index = values.findIndex(e => {
      //     return e.id === id
      //   })
      //
      //   values.splice(index, 1)
      // }
      //
      // this.$emit('input', [...values])
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
