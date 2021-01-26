<template>
  <div class="d-flex justify-content-between mb-4">
    <div class="form-group mr-2">
      <travellers-input
        :value="tempTravellers"
        @input="$emit('travellers', $event)"
      />
    </div>

    <div v-if="hasTimeslots" class="form-group">
      <timeslot-input
        :value="activeTimeslot"
        :timeslots="timeslots"
        @input="$emit('timeslot', $event)"
      />
    </div>
  </div>
</template>

<script>
import TravellersInput from './TravellersInput'
import TimeslotInput from './TimeslotInput'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'

export default {
  components: {
    TimeslotInput,
    TravellersInput
  },

  mixins: [
    clickaway
  ],

  props: {
    travellers: {
      type: Array,
      default: () => []
    },

    timeslots: {
      type: Array,
      default: () => []
    },

    activeTimeslot: {
      type: Object,
      required: true
    },

    hasTimeslots: {
      type: Boolean,
      default: true
    }
  },

  data () {
    return {
      items: [],
      tempTravellers: []
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'cart'
    ])
  },

  watch: {
    travellers: {
      handler (val) {
        this.tempTravellers = [
          ...val
        ]
      },
      immediate: true
    },

    timeslots () {
      //
    }
  }
}
</script>
