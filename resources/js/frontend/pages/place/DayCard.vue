<template>
  <div
    :class="{ 'active-day': active === true }"
    class="card"
    @click.prevent="$emit('selected')"
  >
    <div
      class="card-body"
      style="padding: 6px 0; text-align: center;"
    >
      <div class="date">
        <div
          class="card-text day"
          style="font-size: 18px; text-transform: capitalize; font-weight: 500 !important;"
        >
          {{ day.day }} {{ day.month.substr(0, 3) }}
        </div>

        <div
          class="card-text occupied"
          style="font-weight: 500;"
        >
          <span v-if="day.remainingHours !== false">
            {{ day.remainingHours }} / 18 hrs {{ __('left') }}
          </span>

          <loading-circle
            v-if="day.remainingHours === false"
            :loading="day.remainingHours === false"
            :small="true"
          />
        </div>

        <div v-if="availableFlag">
          <div
            v-if="available"
            style="color: darkgreen;"
          >
            {{ __('Available') }}
          </div>

          <div
            v-else
            style="color: red;"
          >
            {{ __('Not available') }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingCircle from '../../../components/LoadingCircle'

export default {
  components: {
    LoadingCircle
  },

  props: {
    day: {
      type: Object,
      required: true
    },

    availableFlag: {
      type: Boolean,
      default: true
    },

    active: {
      type: Boolean,
      required: true
    },

    available: {
      type: Boolean,
      required: true
    }
  }
}
</script>
