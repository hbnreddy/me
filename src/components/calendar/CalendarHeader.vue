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
                v-if="cityDetails(heading.date) && cityDetails(heading.date).city_id"
                class="city text-uppercase"
              >
                {{ cityDetails(heading.date).city_code }}
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
import {cloneDeep} from 'lodash'
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
      'itinerary'
    ])
  },

  methods: {
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
    },

    cityDetails (date) {
      const itinerary = cloneDeep(this.itinerary)
      itinerary.pop()
      itinerary.shift()

      return itinerary.find(item => item.date === date && item.city_id)
    }
  }
}
</script>
