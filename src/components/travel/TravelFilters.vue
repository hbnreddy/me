<template>
  <div class="filter-list">
    <stops
      v-model="value.max_changes"
      @input="$emit('filter')"
    />

    <bags
      v-model="value.max_bags"
      @input="$emit('filter')"
    />

    <price
      v-model="value.max_price"
      @input="$emit('filter')"
    />

    <airlines
      v-model="value.airlines"
      @input="$emit('filter')"
    />

    <hours
      v-model="value.time"
      @input="$emit('filter')"
    />

    <travel-classes
      v-model="value.classes"
      @input="$emit('filter')"
    />
  </div>
</template>

<script>

import TravelClasses from '../filters/TravelClasses'
import Hours from '../filters/Hours'
import Airlines from '../filters/Airlines'
import Price from '../filters/Price'
import Bags from '../filters/Bags'
import Stops from '../filters/Stops'

export default {
  components: {
    TravelClasses,
    Hours,
    Airlines,
    Price,
    Bags,
    Stops
  },

  props: {
    value: {
      type: Object,
      required: true
    }
  }
}
</script>
