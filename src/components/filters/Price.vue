<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      {{ __('Price') }}
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm sliders"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Price') }}
        </div>

        <div
          class="close"
          @click="closeFilterComponent"
        >
          <i class="fa fa-close" />
        </div>
      </div>

      <div class="body">
        <div class="price-wrap">
          <div class="slider-content">
            <div class="text-primary small d-flex justify-content-between align-items-center mb-2">
              <div>{{ __('Under') }} $ {{ processedPrice }}</div>

              <div class="d-none">
                <a>{{ __('Delete') }}</a>
              </div>
            </div>

            <div class="slider-wrap">
              <vue-slider
                :value="processedPrice"
                :min="pricing.min_price"
                :max="pricing.max_price"
                :interval="pricing.step"
                :height="2"
                :dot-size="20"
                :tooltip="'active'"
                :contained="true"
                @change="onChange($event)"
              >
                <template v-slot:tooltip="{ value }">
                  <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top">
                    <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                      <span class="vue-slider-dot-tooltip-text">
                        {{ __('Under') }} {{ value }}
                      </span>
                    </div>
                  </div>
                </template>
              </vue-slider>
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

  props: {
    value: {
      type: Number,
      default: 0
    }
  },

  data () {
    return {
      currentPrice: 0,
      filterComponent: false,
      pricing: {
        max_price: 5000,
        min_price: 50,
        step: 50
      }
    }
  },

  computed: {
    processedPrice () {
      if (! this.currentPrice) {
        return this.pricing.min_price
      }

      return this.currentPrice
    }
  },

  created () {
    this.currentPrice = this.pricing.min_price
  },

  methods: {
    apply () {
      this.closeFilterComponent()
      this.$emit('input', this.currentPrice)
    },

    onChange (value) {
      this.currentPrice = value
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
