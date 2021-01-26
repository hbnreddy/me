<template>
  <div class="filter-list">
    <brands
      v-model="value.brands"
      @input="$emit('filter')"
    />

    <facilities
      v-model="value.facilities"
      @input="$emit('filter')"
    />

    <guest-rating
      v-model="value.min_rating"
      @input="$emit('filter')"
    />
  </div>
</template>

<script>
import GuestRating from '../filters/GuestRating'
import Facilities from '../filters/Facilities'
import Brands from '../filters/Brands'

export default {
  components: {
    GuestRating,
    Facilities,
    Brands
  },

  props: {
    value: {
      type: Object,
      required: true
    }
  }
}
</script>
