<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      {{ __('Hours') }}
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm sliders"
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

      <div class="body">
        <ul
          v-if="multiple"
          id="myTab"
          class="nav nav-tabs hours-tabs"
          role="tablist"
        >
          <li class="nav-item flex-fill">
            <a
              id="home-tab"
              class="nav-link active"
              data-toggle="tab"
              href="#home"
              role="tab"
              aria-controls="home"
              aria-selected="true"
            >1</a>
          </li>

          <li class="nav-item flex-fill">
            <a
              id="profile-tab"
              class="nav-link"
              data-toggle="tab"
              href="#profile"
              role="tab"
              aria-controls="profile"
              aria-selected="false"
            >2</a>
          </li>
        </ul>

        <div
          id="myTabContent"
          class="tab-content"
        >
          <div
            id="home"
            class="tab-pane slider-content fade show active"
            role="tabpanel"
            aria-labelledby="home-tab"
          >
            <div class="p-1 mb-2 route">
              {{ __('From') }} Zurich {{ __('to') }} Paris
            </div>

            <div class="hour-wrap mb-2">
              <label :for="'hourRange1'">
                <i class="fa fa-plane icon" />
                {{ __('Departure') }}
              </label>

              <div class="slider-wrap">
                <vue-slider
                  :value="departureInterval"
                  :min="1"
                  :max="24"
                  :interval="1"
                  :height="2"
                  :dot-size="20"
                  :tooltip="'active'"
                  :contained="true"
                  :enable-cross="false"
                  :min-range="3"
                  @change="onInputDeparture($event)"
                >
                  <template v-slot:tooltip="{value}">
                    <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top">
                      <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                        <span class="vue-slider-dot-tooltip-text">
                          {{ value }}:00
                        </span>
                      </div>
                    </div>
                  </template>
                </vue-slider>
              </div>
            </div>

            <div class="hour-wrap mb-2">
              <label :for="'hourRange2'">
                <i class="fa fa-plane icon" />
                Arrival
              </label>

              <div class="slider-wrap">
                <vue-slider
                  :value="arrivalInterval"
                  :min="1"
                  :max="24"
                  :interval="1"
                  :height="2"
                  :dot-size="20"
                  :tooltip="'active'"
                  :contained="true"
                  :enable-cross="false"
                  :min-range="3"
                  @change="onInputArrival($event)"
                >
                  <template v-slot:tooltip="{value}">
                    <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top">
                      <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                        <span class="vue-slider-dot-tooltip-text">
                          {{ value }}:00
                        </span>
                      </div>
                    </div>
                  </template>
                </vue-slider>
              </div>
            </div>
          </div>

          <div
            v-if="multiple"
            id="profile"
            class="tab-pane slider-content fade"
            role="tabpanel"
            aria-labelledby="profile-tab"
          >
            <div class="p-1 mb-2 route">
              {{ __('From') }} Paris {{ __('to') }} Zurich
            </div>

            <div class="hour-wrap mb-2">
              <label :for="'hourRange1'">
                <i class="fa fa-plane icon" />
                {{ __('Departure') }}
              </label>

              <div class="slider-wrap">
                <vue-slider
                  v-model="values"
                  :min="1"
                  :max="24"
                  :interval="1"
                  :height="2"
                  :dot-size="20"
                  :tooltip="'active'"
                  :contained="true"
                  :enable-cross="false"
                  :min-range="3"
                >
                  <template v-slot:tooltip="{value}">
                    <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top">
                      <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                        <span class="vue-slider-dot-tooltip-text">
                          {{ value }}:00
                        </span>
                      </div>
                    </div>
                  </template>
                </vue-slider>
              </div>
            </div>

            <div class="hour-wrap mb-2">
              <label :for="'hourRange2'">
                <i class="fa fa-plane icon" />
                {{ __('Arrival') }}
              </label>

              <div class="slider-wrap">
                <vue-slider
                  v-model="values2"
                  :min="1"
                  :max="24"
                  :interval="1"
                  :height="2"
                  :dot-size="20"
                  :tooltip="'active'"
                  :contained="true"
                  :enable-cross="false"
                  :min-range="3"
                >
                  <template v-slot:tooltip="{value}">
                    <div class="vue-slider-dot-tooltip vue-slider-dot-tooltip-top">
                      <div class="vue-slider-dot-tooltip-inner vue-slider-dot-tooltip-inner-top">
                        <span class="vue-slider-dot-tooltip-text">
                          {{ value }}:00
                        </span>
                      </div>
                    </div>
                  </template>
                </vue-slider>
              </div>
            </div>
          </div>
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
      type: Object | Array,
      default: () => {}
    },

    multiple: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      filterComponent: false,
      departureInterval: [1, 24],
      arrivalInterval: [1, 24],
      values: [1, 24],
      values2: [1, 24]
    }
  },

  watch: {
    value: {
      handler () {
        if (!this.multiple && this.value) {
          this.departureInterval = [
            ...this.value.departure
          ]

          this.arrivalInterval = [
            ...this.value.arrival
          ]
        } else {
          //
        }
      },
      immediate: true
    }
  },

  methods: {
    onInputDeparture (value) {
      this.$emit('input', {
        departure: value,
        arrival: this.arrivalInterval
      })
    },

    onInputArrival (value) {
      this.$emit('input', {
        departure: this.departureInterval,
        arrival: value
      })
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
