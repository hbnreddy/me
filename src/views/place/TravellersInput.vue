<template>
  <div class="travellers-input">
    <div
      class="cursor-pointer"
      @click.prevent="picker = true"
    >
      <input
        :value="inputText"
        type="text"
        class="form-control cursor-pointer"
        placeholder="Travellers"
        disabled
      >

      <i class="fa fa-angle-down arrow" />
    </div>

    <travellers-selector
      v-if="picker"
      v-on-clickaway="hidePicker"
      :values="tempTravellers"
      @input="$emit('input', $event)"
      @hide="hidePicker()"
    />
  </div>
</template>

<script>
import TravellersSelector from '../../components/travellers/TravellersSelector'

import {mixin as clickaway} from 'vue-clickaway'

export default {
  components: {
    TravellersSelector
  },

  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      tempTravellers: [],
      picker: false
    }
  },

  computed: {
    inputText () {
      return 'Travellers ' + (this.travellersCount('adults')
        + this.travellersCount('children')
        + this.travellersCount('infants'))
    }
  },

  watch: {
    value: {
      handler (val) {
        this.tempTravellers = [
          ...val
        ]
      },
      immediate: true
    }
  },

  methods: {
    travellersCount (type) {
      return this.tempTravellers.filter(e => {
        return e.active && e.type === type
      }).length
    },

    hidePicker () {
      this.picker = false
    }
  }
}
</script>
