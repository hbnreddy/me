<template>
  <div class="dropdown-menu show mt-1 hour-picker" style="position: absolute;">
    <div
      v-for="(timeslot, index) in timeslots"
      :key="index"
      class="dropdown-item dropdown-hover"
      style="padding: 4px;"
      @click.prevent="applyHourSelection(index)"
    >
      <i class="fa fa-clock-o mr-1" />

      <span v-if="interval">
        {{ timeslot.start_time }} - {{ timeslot.end_time }}
      </span>

      <span v-else>
        {{ timeslot.start_time }}
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    timeslots: {
      type: Array,
      required: true
    },

    interval: {
      type: Boolean,
      default: false
    }
  },

  methods: {
    applyHourSelection (index) {
      this.$emit('input', index)
    }
  }
}
</script>
