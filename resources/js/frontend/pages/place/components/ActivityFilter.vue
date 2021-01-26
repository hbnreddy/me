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
          <div v-if="filter.key === 'duration'">
            <div>{{ filter.title }}</div>
            <i class="fa fa-chevron-down" />
          </div>

          <div
            v-else
            class="custom-control custom-checkbox"
          >
            <input
              :id="'customCheck' + index"
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
                type="checkbox"
                class="custom-control-input"
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
import {cloneDeep} from 'lodash'

export default {
  props: {
    filters: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      activeFilters: {
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

  methods: {
    apply () {
      this.$emit('filter', cloneDeep(this.mainFilter))
    },

    filterChanged (key, value) {
      this.activeFilters[key] = value

      this.processFilters()
    },

    processFilters () {
      this.mainFilter.duration = this.processDuration()

      Object.keys(this.mainFilter.refundable).forEach(key => {
        this.mainFilter.refundable[key] = this.activeFilters[key]
      })

      Object.keys(this.mainFilter.pass_type).forEach(key => {
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
