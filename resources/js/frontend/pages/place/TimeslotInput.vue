<template>
  <div class="timeslot-input" style="position: relative;">
    <div
      class="cursor-pointer"
      @click.prevent="picker = true"
    >
      <input
        :value="inputText"
        type="text"
        class="form-control cursor-pointer"
        disabled
      >

      <i class="fa fa-angle-down arrow" />
    </div>

    <hour-picker
      v-if="picker"
      v-on-clickaway="hidePicker"
      :timeslots="timeslots"
      style="width: 100px; right: 0;"
      @input="onInput($event)"
    />
  </div>
</template>

<script>
import HourPicker from '../../components/HourPicker'

import {mixin as clickaway} from 'vue-clickaway'

export default {
  components: {
    HourPicker
  },

  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Object | Boolean,
      default: false
    },

    timeslots: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      activeTimeslot: false,
      picker: false
    }
  },

  computed: {
    inputText () {
      if (!this.activeTimeslot || !this.activeTimeslot.start_time) {
        return ''
      }

      let text = this.activeTimeslot.start_time

      if (this.activeTimeslot.end_time) {
        text = `${text} - ${this.activeTimeslot.end_time}`
      }

      return text
    }
  },

  watch: {
    value: {
      handler (value) {
        this.activeTimeslot = {
          ...value
        }
      },
      immediate: true
    }
  },

  methods: {
    onInput (value) {
      this.picker = false

      this.$emit('input', value)
    },

    hidePicker () {
      this.picker = false
    }
  }
}
</script>
