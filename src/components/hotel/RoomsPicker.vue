<template>
  <div class="guests-picker py-2">
    <div class="rooms-list">
      <room-row
        v-for="(room, index) in tempRooms"
        :key="index"
        v-model="tempRooms[index]"
        :number="index + 1"
      />
    </div>

    <a
      class="btn-add-room"
      @click.prevent="addRoom()"
    >
      Add Room <i class="fa fa-plus-circle" />
    </a>

    <div class="options d-flex justify-content-end">
      <a
        href="#"
        @click.prevent="save()"
      >
        Save
      </a>
    </div>
  </div>
</template>

<script>
import RoomRow from './RoomRow'

import { mapGetters } from 'vuex'

export default {
  components: {
    RoomRow
  },

  props: {
    value: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      tempRooms: [],
      roomModel: {
        guests: {
          adults: 0,
          children: 0,
          infants: 0
        }
      }
    }
  },

  computed: {
    travellersModel () {
      return this.travellers.map(e => {
        return {
          id: e.id,
          reference_key: e.reference_key,
          active: false,
          type: e.type
        }
      })
    }
  },

  watch: {
    value: {
      handler () {
        this.tempRooms = [
          ...this.value
        ]
      },
      immediate: true
    }
  },

  methods: {
    addRoom () {
      this.tempRooms.push({
        ...this.roomModel,
        guests: this.travellersModel
      })
    },

    save () {
      this.$emit('input', [
        ...this.tempRooms
      ])
    },

    onCancel () {
      this.$emit('hide')
    }
  }
}
</script>
