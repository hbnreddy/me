<template>
  <div class="guests-picker">
    <div class="guests-list">
      <div class="text-muted mr-2">
        Guests
      </div>
      <div class="list">
        <div
          v-for="(guest, index) in travellers"
          :key="index"
        >
          <div
            v-for="i in guest"
            :key="i"
            :class="{'selected': selectedGuests.includes(i)}"
            class="guest"
          >
            {{ index.substr(0, 1) }} {{ i }}
          </div>
        </div>
      </div>
    </div>

    <div class="rooms-list">
      <room-row
        v-for="(room, index) in rooms"
        :key="index"
        :number="index + 1"
        :room="room"
      />
    </div>

    <a
      class="btn-add-room"
      @click.prevent="addRoom()"
    >
      Add Room <i class="fa fa-plus-circle" />
    </a>
  </div>
</template>

<script>
import RoomRow from './RoomRow'

export default {
  components: {
    RoomRow
  },

  data () {
    return {
      travellers: {
        adults: 3,
        children: 2,
        infants: 1
      },
      roomModel: {
        guests: {
          adults: 2,
          children: 3,
          infants: 0
        },
        type: 'single'
      },
      rooms: [
        {
          guests: {
            adults: 2,
            children: 3,
            infants: 0
          },
          type: 'single'
        },
        {
          guests: {
            adults: 5,
            children: 1,
            infants: 2
          },
          type: 'double'
        }
      ],
      selectedGuests: [1, 3, 4, 5]
    }
  },

  methods: {
    addRoom () {
      this.rooms.push(this.roomModel)
    },

    onCancel () {
      this.$emit('hide')
    }
  }
}
</script>
