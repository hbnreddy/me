<template>
  <div class="other-themes">
    <h6 class="font-weight-bold mb-3">
      {{ __('Other places based on your interests') }}
    </h6>

    <div class="interests-base-places-list">
      <a
        v-for="place in places"
        :key="place.id"
        href="#"
        class="place"
        @click.prevent="goToPlace(place.id)"
      >
        {{ place.name }}
      </a>
    </div>
  </div>
</template>

<script>
import { route } from '../../../mixins/route'

export default {
  mixins: [
    route
  ],

  props: {
    places: {
      type: Array,
      default: () => []
    }
  },

  methods: {
    goToPlace (id) {
      this.$redirect(`place/${id}`, this.routeParams)
    },

    processReviewRating (rating) {
      if (!rating) {
        return []
      }

      let ratings = []
      for (let i = 0; i < 5; ++i) {
        ratings.push({
          rate: false
        })
      }

      for (let i = 0; i < rating; ++i) {
        ratings[i].rate = true
      }

      return ratings
    }
  }
}
</script>
