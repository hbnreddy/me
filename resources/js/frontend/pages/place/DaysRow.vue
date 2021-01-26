<template>
  <div class="d-flex justify-content-center">
    <div class="carousel-days">
      <carousel
        :per-page-custom="[[320, 4], [425, 7]]"
        :mouse-drag="true"
        :autoplay="false"
        :pagination-enabled="false"
        :navigation-enabled="true"
        :navigation-next-label="'<div><i class=\'fa fa-angle-right slide-next\'></i></div>'"
        :navigation-prev-label="'<div><i class=\'fa fa-angle-left slide-prev\'></i></div>'"
        style="max-height: 100px;"
      >
        <slide
          v-for="(day, index) in days"
          :key="index"
          style="transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);"
        >
          <day-card
            :day="day"
            :active="active === index"
            :available-flag="availableFlag"
            :available="day.available"
            @selected="setActiveDay(index)"
          />
        </slide>
      </carousel>
    </div>
  </div>
</template>

<script>
import DayCard from './DayCard'
import LoadingCircle from '../../../components/LoadingCircle'

export default {
  components: {
    DayCard,
    LoadingCircle
  },

  props: {
    days: {
      type: Array,
      default: () => []
    },

    availableDays: {
      type: Array,
      default: () => []
    },

    active: {
      type: Number,
      default: 0
    },

    availableFlag: {
      type: Boolean,
      default: true
    },

    loading: {
      type: Boolean,
      default: false
    }
  },

  methods: {
    setActiveDay (index) {
      this.$emit('change', index)
    },

    isDayAvailable (day) {
      return this.availableDays.includes(day)
    }
  }
}
</script>
