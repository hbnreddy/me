<template>
  <div class="city-weather-info">
    <span class="label">{{ __('Best time to visit') }}</span>

    <span>
      {{ print() }}

      <span v-if="!monthsCount">-</span>

      <a
        v-if="monthsCount > 2 && !showMore"
        href="#"
        class="text-decoration-none"
        @click.prevent="setShowMore(true)"
      >
        {{ __('Show more') }}..
      </a>

      <a
        v-else-if="monthsCount > 2"
        href="#"
        class="text-decoration-none"
        @click.prevent="setShowMore(false)"
      >
        {{ __('Show less') }}..
      </a>
    </span>
  </div>
</template>

<script>
import { capitalize } from 'lodash'

export default {
  props: {
    weathers: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      showMore: false
    }
  },

  computed: {
    monthsCount () {
      return this.weathers.length
    }
  },

  methods: {
    setShowMore (state) {
      this.showMore = state
    },

    print () {
      let months = this.weathers
        .map(e => {
          return capitalize(e)
        })

      if (this.showMore) {
        return months.splice(1, months.length - 1)
          .join(', ') +
            ' & ' + months[months.length - 1]
      }

      let text = ''
      if (months.length >= 3) {
        text = months
          .slice(1, 3)
          .join(', ')

        text = text + ' & ' + (months.length - 2) + ' more'

        return text
      } else {
        return months
          .join(' & ')
      }
    }
  }
}
</script>
