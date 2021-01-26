<template>
  <div class="room-row">
    <div class="left-side">
      <div class="mb-1">
        {{ __('Room') }} {{ number }}
      </div>

      <div>
        <i
          class="fa fa-users"
          style="color: #AFC1DD;"
        />
        <span class="">3</span>
      </div>
    </div>

    <div class="room-type">
      <a
        class="text-decoration-none room-type"
        @click.prevent="toggleRoomType()"
      >
        {{ currentRoomType }} <i class="fa fa-arrow-down" />
      </a>

      <div
        v-if="roomType"
        class="room-type-dropdown"
      >
        <ul class="droplist">
          <li
            v-for="(type, index) in roomTypes"
            :key="index"
            class="dropitem"
          >
            <a
              :class="{active: selectedRoomType === index}"
              href="#"
              @click.prevent="selectRoomType(index)"
            >{{ type }}</a>
          </li>
        </ul>
      </div>
    </div>

    <div class="room-selector">
      <a
        href="#"
        class="btn-guests-selector"
        @click.prevent="toggleGuestsSelector()"
      >
        {{ __('Add Guests') }} <i class="fa fa-arrow-circle-down" />
      </a>

      <travellers-picker-extended
        v-if="guestsSelector"
        :value="room.guests"
        @input="guestsSelector = false"
      />
    </div>

    <div class="picked-guests">
      <div class="guest-box">
        A1
      </div>
      <div class="guest-box">
        A2
      </div>
      <div class="guest-box">
        C1
      </div>
    </div>
  </div>
</template>

<script>
import TravellersPickerExtended from './TravellersPickerExtended'

export default {
  components: {
    TravellersPickerExtended
  },
  props: {
    room: {
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
      roomTypes: [
        'Single',
        'Double'
      ],
      guestsSelector: false,
      roomType: false,
      guests: [
        'A1',
        'A2',
        'C1',
        'I1'
      ],
      selectedRoomType: false
    }
  },

  computed: {
    currentRoomType () {
      return this.selectedRoomType !== false
        ? this.roomTypes[this.selectedRoomType]
        : 'Select'
    }
  },

  created () {
    this.selectedRoomType = this.roomTypes.findIndex(t => {
      return t.toLowerCase() === this.room.type.toLowerCase()
    })

    if (this.selectedRoomType < 0) {
      this.selectedRoomType = false
    }
  },

  methods: {
    toggleGuestsSelector () {
      this.guestsSelector = !this.guestsSelector
    },

    toggleRoomType () {
      this.roomType = !this.roomType
    },

    selectRoomType (index) {
      this.selectedRoomType = index

      this.roomType = false
    }
  }
}
</script>
