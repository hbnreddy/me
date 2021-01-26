<template>
  <div class="room-row">
    <div class="left-side">
      <div class="mb-1 mr-3">
        {{ __('Room') }} {{ number }}
      </div>

      <div>
        <i
          class="fa fa-users"
          style="color: #AFC1DD;"
        />
        <span class="">{{ activeGuests.length }}</span>
      </div>
    </div>

    <div class="room-selector">
      <a
        href="#"
        class="btn-guests-selector"
        @click.prevent="toggleGuestsSelector()"
      >
        {{ __('Edit Guests') }} <i class="fa fa-arrow-circle-down" />
      </a>

      <travellers-picker-extended
        v-if="guestsSelector"
        v-on-clickaway="toggleGuestsSelector"
        :travellers="value.guests"
        @change="onInputTravellers($event)"
        @hide="guestsSelector = false"
      />
    </div>

    <div class="picked-guests">
      <div
        v-for="(guest, index) in activeGuests"
        :key="index"
        class="guest-box"
      >
        {{ guest.reference_key }}
      </div>
    </div>
  </div>
</template>

<script>
import TravellersPickerExtended from '../travellers/TravellersPickerExtended'

import {mixin as clickaway} from 'vue-clickaway'

export default {
  components: {
    TravellersPickerExtended
  },

  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Object,
      required: true
    },

    number: {
      type: Number,
      required: true
    }
  },

  data () {
    return {
      guestsSelector: false
    }
  },

  computed: {
    activeGuests () {
      return this.value.filter(e => {
        return e.active === true
      })
    }
  },

  methods: {
    onInputTravellers (guests) {
      this.$emit('input', {
        ...this.value,
        guests
      })
    },

    toggleGuestsSelector () {
      this.guestsSelector = !this.guestsSelector
    }
  }
}
</script>
