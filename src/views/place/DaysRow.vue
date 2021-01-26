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
          v-for="(day, key, index) in days"
          :key="index"
          style="transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);"
        >
          <transition name="fade">
            <day-card
              :day="day"
              :active="activeDate === key"
              :available-flag="availableFlag"
              :available="!!day.available"
              @selected="setActiveDate(key)"
            />
          </transition>
        </slide>
      </carousel>
    </div>
  </div>
</template>

<script>
import DayCard from './DayCard'

export default {
  components: {
    DayCard
  },

  props: {
    days: {
      type: Object,
      default: () => {
        //
      }
    },

    activeDate: {
      type: String,
      required: true
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
    setActiveDate (date) {
      this.$emit('change', date)
    }
  }
}
</script>
