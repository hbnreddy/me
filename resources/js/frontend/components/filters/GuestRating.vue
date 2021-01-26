<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >{{ __('Guest rating') }}</a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm sliders"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Minimum guest evaluation') }}
        </div>

        <div class="small text-muted">
          5 {{ __('locations') }}
        </div>
      </div>

      <div class="body">
        <div class="slider-content">
          <div class="slider-wrap rating">
            <vue-slider
              v-model="rating.current_rating"
              :min="rating.min_rating"
              :max="rating.max_rating"
              :interval="rating.step"
              :height="3"
              :dot-size="20"
              :contained="true"
              :data="['0,0', '0,5', '1,0', '1,5', '2,0', '2,5', '3,0', '3,5', '4,0', '4,5', '5,0']"
              :tooltip="'always'"
              :hide-label="false"
              :label="'cuc'"
            >
              <template v-slot:tooltip="{ active, value }">
                <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top vue-slider-dot-tooltip-show">
                  <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                    <span class="vue-slider-dot-tooltip-text">
                      <i class="fa fa-star mr-2" />
                      {{ value }}
                    </span>

                    <span class="expanded-tooltip-text">
                      {{ formatTooltip(value) }}
                    </span>
                  </div>
                </div>
              </template>
            </vue-slider>
          </div>
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
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'
import {mixin as clickaway} from 'vue-clickaway'

export default {
  components: {
    VueSlider
  },

  mixins: [
    clickaway
  ],

  data () {
    return {
      filterComponent: false,
      rating: {
        current_rating: '4,0',
        max_rating: 5,
        min_rating: 0,
        step: 0.5
      },
      ratingList: {
        '0,0': 'Mediocre',
        '0,5': 'Mediocre',
        '1,0': 'Mediocre',
        '1,5': 'Mediocre',
        '2,0': 'Acceptable',
        '2,5': 'Acceptable',
        '3,0': 'Acceptable',
        '3,5': 'Good',
        '4,0': 'Very good',
        '4,5': 'Excellent',
        '5,0': 'Excellent'
      }
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

    formatTooltip (value) {
      return this.ratingList[value]
    }
  }
}
</script>
