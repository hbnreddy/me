<template>
  <div class="header vuecal__header">
    <div class="vuecal__title-bar">
      <button
        aria-label="Previous week"
        class="vuecal__arrow vuecal__arrow--prev"
        @click="showPrev(selectedChunk)"
      >
        <i class="fa fa-angle-left" />
      </button>

      <button
        aria-label="Next week"
        class="vuecal__arrow vuecal__arrow--next"
        @click="showNext(selectedChunk)"
      >
        <i class="fa fa-angle-right" />
      </button>
    </div>

    <div class="vuecal__flex vuecal__weekdays-headings weekdays-headings">
      <div
        v-for="(chunk, chunkIndex) in daysChunks"
        :key="chunkIndex"
        :class="[
          {'selected-chunk' : chunkIndex === selectedChunk},
          'chunk-' + chunkIndex
        ]"
        class="chunk"
      >
        <div
          v-for="(heading, index) in chunk"
          :key="index"
          :class="{'selected-date' : isCurrentDate(heading.date)}"
          class="vuecal__flex vuecal__heading"
        >
          <div class="vuecal__flex">
            <div class="vuecal__flex weekday-label">
              <div
                v-if="citiesPerDates[heading.date] && citiesPerDates[heading.date].id"
                class="city text-uppercase"
              >
                {{ citiesPerDates[heading.date].name }}
              </div>

              <div
                v-else
                class="city text-capitalize"
              >
                {{ __('No city') }}
              </div>

              <div class="day-of-week">
                {{ moment(heading.date).format('ddd') }}
              </div>

              <div class="day">
                {{ moment(heading.date).format('DD') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import * as moment from 'moment'
import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  props: {
    daysChunks: {
      type: Array,
      default: () => []
    },

    selectedChunk: {
      type: Number,
      default: () => 1
    }
  },

  data () {
    return {
      citiesPerDates: {
        //
      }
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ])
  },

  created () {
    this.fetchCitiesPerDates()
  },

  methods: {
    async fetchCitiesPerDates () {
      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/cities-per-dates`)
        .then(r => {
          if (r.data.success) {
            this.citiesPerDates = r.data.entities
          }
        })
    },

    showPrev (index) {
      this.$emit('selected', index ? index - 1 : 0)
    },

    showNext (index) {
      if ((this.selectedChunk + 1) === this.daysChunks.length) {
        return false
      }

      this.$emit('selected', index + 1)
    },

    showCurrentChunk () {
      document.querySelectorAll('.chunk').forEach(e => {
        e.style.display = 'none'
      })
      document.querySelector(`.chunk.chunk-${this.selectedChunk}`).style.display = 'flex'
    },

    isCurrentDate (date) {
      return moment(date).format(this.$const('BASE_DATE_FORMAT'))
        === moment().format(this.$const('BASE_DATE_FORMAT'))
    }
  }
}
</script>
