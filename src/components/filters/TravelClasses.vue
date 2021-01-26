<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      {{ __('Class') }}
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Travel Class') }}
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
          <div>{{ __('Select all classes') }}</div>

          <toggle-input
            :enabled="!value.length"
          />
        </div>

        <div>
          <div class="text-capitalize type">
            Classes
          </div>

          <div
            v-for="(travelClass, travelClassIndex) in classes"
            :key="travelClassIndex"
          >
            <div class="form-group custom-control custom-checkbox">
              <input
                :id="'a-' + travelClassIndex"
                :checked="activeClasses.includes(travelClass.key)"
                type="checkbox"
                class="custom-control-input"
                @change="onChange(travelClass.key)"
              >

              <label
                :for="'a-' + travelClassIndex"
                class="custom-control-label"
              >
                {{ travelClass.text }}
              </label>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end px-4">
          <a
            class="btn-link cursor-pointer"
            @click.prevent="apply()"
          >
            Apply
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'
import ToggleInput from '../../components/ToggleInput'

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
    }
  },

  data () {
    return {
      activeClasses: [],
      classes: [
        {
          key: 'f',
          text: 'First'
        },
        {
          key: 'ewoutr',
          text: 'Economy Without Restrictions'
        },
        {
          key: 'ewr',
          text: 'Economy With Restrictions'
        },
        {
          key: 'ep',
          text: 'Economy Premium'
        },
        {
          key: 'b',
          text: 'Business'
        }
      ],
      filterComponent: false
    }
  },

  watch: {
    value: {
      handler () {
        this.activeClasses = [
          ...this.value
        ]
      },
      immediate: true
    }
  },

  methods: {
    apply () {
      this.$emit('input', [
        ...this.activeClasses
      ])

      this.closeFilterComponent()
    },

    onChange (travelClass) {
      let classes = [
        ...this.activeClasses
      ]

      const index = classes.findIndex((el) => {
        return el === travelClass
      })

      if (index >= 0) {
        classes.splice(index, 1)
      } else {
        classes.push(travelClass)
      }

      this.activeClasses = [
        ...classes
      ]
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
