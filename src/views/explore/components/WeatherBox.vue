<template>
  <div class="weather-box">
    <div class="best-time">
      <div class="title">
        {{ __('Best time to visit') }}
      </div>

      <div class="months-list">
        <span
          v-for="value in weathers"
          :key="value.id"
          :class="{active: value.id === activeWeather.id}"
          class="text-decoration-none"
          @click="onChange(value)"
        >
          {{ printMonth(value.month) }}
        </span>
      </div>
    </div>

    <div class="weather">
      <div class="stats">
        <div class="precipitations mr-1">
          <i class="fa fa-tint" />
          {{ activeWeather.precipitation }}&nbsp;%
        </div>

        <div class="high mr-1">
          <i class="fa fa-long-arrow-up" />
          {{ activeWeather.high }}&nbsp;°C
        </div>

        <div class="low mr-1">
          <i class="fa fa-long-arrow-down" />
          {{ activeWeather.low }}&nbsp;°C
        </div>
      </div>

      <div class="average">
        <div class="image">
          <i class="fa  fa-cloud" />
        </div>

        <div>
          {{ activeWeather.average }} °C
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {capitalize} from 'lodash'

export default {
  props: {
    weathers: {
      type: Array | Object,
      required: true
    }
  },

  data () {
    return {
      activeWeather: null
    }
  },

  created () {
    this.activeWeather = Object.values(this.weathers)[0]
  },

  methods: {
    onChange (weather) {
      this.activeWeather = weather
    },

    printMonth (month) {
      return capitalize(month.toString()
        .substr(0, 3))
    }
  }
}
</script>
