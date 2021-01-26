<template>
  <div class="filter-dropdown shadow-sm">
    <div
      id="accordionExample"
      class="accordion"
    >
      <div
        v-for="(filter, index) in newFiltersList"
        :key="index"
        class="card"
      >
        <div
          :id="'heading' + filter.title.replace(' ', '')"
          :data-target="'#collapse' + filter.title.replace(' ', '')"
          :aria-controls="'collapse' + filter.title.replace(' ', '')"
          class="card-header"
          data-toggle="collapse"
          aria-expanded="true"
        >
          <div v-if="['price_range', 'duration'].includes(filter.key)">
            <div>{{ filter.title }}</div>
            <i class="fa fa-chevron-down d-none" />
          </div>

          <div
            v-else
            class="custom-control custom-checkbox"
          >
            <input
              :id="'customCheck' + index"
              :checked="mainFilter[filter.key]"
              type="checkbox"
              class="custom-control-input"
              @change="filterChanged(filter.key, $event.target.checked)"
            >

            <label
              :for="'customCheck' + index"
              class="custom-control-label"
            >
              {{ __(filter.title) }}
            </label>
          </div>
        </div>

        <div
          v-if="filter.key === 'price_range'"
          class="slider-content"
        >
          <div class="slider-wrap">
            <vue-slider
              v-model="mainFilter.price"
              :min="minPrice"
              :max="maxPrice"
              :interval="50"
              :height="4"
              :dot-size="20"
              :tooltip="'always'"
              :contained="true"
              :enable-cross="false"
            >
              <template v-slot:tooltip="{value}">
                <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top">
                  <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                    <span class="vue-slider-dot-tooltip-text">
                      {{ value }}
                    </span>
                  </div>
                </div>
              </template>
            </vue-slider>
          </div>
        </div>

        <div
          v-else
          :id="'collapse' + filter.title.replace(' ', '')"
          :aria-labelledby="'heading' + filter.title.replace(' ', '')"
          class="collapse show"
        >
          <div
            v-if="filter.filters"
            class="card-body"
          >
            <div
              v-for="(item, itemIndex) in filter.filters"
              :key="itemIndex"
              class="custom-control custom-checkbox"
            >
              <input
                :id="'customCheck' + index + '-' + itemIndex"
                :checked="isChecked(item)"
                type="checkbox"
                class="custom-control-input cursor-point-hover"
                @change="filterChanged(item.key, $event.target.checked)"
              >

              <label
                :for="'customCheck' + index + '-' + itemIndex"
                class="custom-control-label"
              >
                {{ item.item }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="float-right px-2">
      <a
        href="#"
        class="text-decoration-none"
        style="color: #0c66f1;"
        @click.prevent="apply()"
      >
        {{ __('Apply') }}
      </a>
    </div>
  </div>
</template>

<script>
import VueSlider from 'vue-slider-component'

import {cloneDeep} from 'lodash'

export default {
  components: {
    VueSlider
  },

  props: {
    filters: {
      type: Object,
      required: true
    },

    priceRange: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      activeFilters: {
        pickup: false,
        '0_2': false,
        '2_4': false,
        '4_8': false,
        full_refund: false,
        half_refund: false,
        no_refund: false,
        skip_line: false,
        multi_day: false,
        entrance_only: false,
        combination: false,
        meal_included: false
      },
      newFiltersList: [
        {
          key: 'duration',
          title: 'Duration',
          filters: [
            {
              item: '0 - 2 Hours',
              key: '0_2'
            },
            {
              item: '2 - 4 Hours',
              key: '2_4'
            },
            {
              item: '4 - 8+ Hours',
              key: '4_8'
            }
          ]
        },
        {
          key: 'price_range',
          title: 'Price range'
        },
        {
          key: 'pickup',
          title: 'Hotel Pick Up'
        }
      ],
      filtersList: [
        {
          key: 'duration',
          title: 'Duration',
          filters: [
            {
              item: '0 - 2 Hours',
              key: '0_2'
            },
            {
              item: '2 - 4 Hours',
              key: '2_4'
            },
            {
              item: '4 - 8+ Hours',
              key: '4_8'
            }
          ]
        },
        {
          title: 'Refundable',
          filters: [
            {
              item: 'Fully-Refundable',
              key: 'full_refund'
            },
            {
              item: 'Partly-Refundable',
              key: 'half_refund'
            },
            {
              item: 'Non-Refundable',
              key: 'no_refund'
            }
          ]
        },
        {
          title: 'Pass Type',
          filters: [
            {
              item: 'Skip the Line',
              key: 'skip_line'
            },
            {
              item: 'Multi-day',
              key: 'multi_day'
            },
            {
              item: 'Entrance Only',
              key: 'entrance_only'
            },
            {
              item: 'Combination',
              key: 'combination'
            }
          ]
        },
        {
          title: 'Meals Offered',
          key: 'meal_included'
        }
      ],
      mainFilter: {
        pickup: false,
        price: 0,
        meal_included: false,
        duration: {
          min: 0,
          max: 100
        },
        refundable: {
          full_refund: false,
          half_refund: false,
          no_refund: false
        },
        pass_type: {
          skip_line: false,
          multi_day: false,
          entrance_only: false,
          combination: false
        }
      }
    }
  },

  watch: {
    filters: {
      handler () {
        this.mainFilter.price = this.filters.price
        this.mainFilter.pickup = this.filters.pickup
      },
      immediate: true
    }
  },

  computed: {
    minMaxDiff () {
      return this.priceRange.max - this.priceRange.min
    },

    minPrice () {
      if (this.priceRange.min % 50 === 0) {
        return this.priceRange.min
      }

      if (this.priceRange.min < 50) {
        return 0
      }

      return 50
    },

    maxPrice () {
      return this.priceRange.max % 50 === 0
        ? this.priceRange.max
        : this.priceRange.max + (50 - this.priceRange.max % 50)
    }
  },

  methods: {
    isChecked (item) {
      if (this.filters.duration) {
        const min = parseInt(this.filters.duration.min)
        const max = parseInt(this.filters.duration.max)

        const splittedKey = item.key.split('_')

        const keyMin = parseInt(splittedKey[0])
        const keyMax = parseInt(splittedKey[1])

        return keyMin >= min && keyMax <= max
      }

      return !!this.filters[item.key]
    },

    apply () {
      this.$emit('filter', cloneDeep(this.mainFilter))
    },

    filterChanged (key, value) {
      this.activeFilters[key] = value

      this.processFilters()
    },

    processFilters () {
      this.mainFilter.duration = this.processDuration()
      this.mainFilter.pickup = this.activeFilters.pickup

      Object.keys(this.mainFilter.refundable)
        .forEach(key => {
          this.mainFilter.refundable[key] = this.activeFilters[key]
        })

      Object.keys(this.mainFilter.pass_type)
        .forEach(key => {
          this.mainFilter.pass_type[key] = this.activeFilters[key]
        })
    },

    processDuration () {
      let min = 0
      let max = 100

      if (this.activeFilters['4_8']) {
        min = 4
        max = 100
      }

      if (this.activeFilters['2_4']) {
        min = 2

        if (!this.activeFilters['4_8']) {
          max = 4
        }
      }

      if (this.activeFilters['0_2']) {
        min = 0

        if (!this.activeFilters['4_8'] && !this.activeFilters['2_4']) {
          max = 2
        }
      }

      return {
        min,
        max
      }
    }
  }
}
</script>
