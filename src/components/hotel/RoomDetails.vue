<template>
  <div class="card shadow-sm">
    <div v-if="1 === 0" class="card-header">
      <div class="room-wrap d-flex justify-content-between">
        <div class="name">
          Pack {{ index + 1 }}
        </div>

        <div class="guests">
          {{ __('Guests') }}
          <i class="fa fa-users icon" />

          <span class="badge small">4</span>
        </div>

        <div class="pack">
          <span>{{ __('Superior') }}</span>
        </div>

        <div class="toggle-icon">
          <i class="fa fa-angle-down" />
        </div>
      </div>
    </div>

    <div class="card-body">
      <div
        v-for="(room, roomIndex) in offerRooms"
        :key="roomIndex"
        class="room-wrap"
      >
        <div class="type">
          {{ room.type }}
        </div>

        <div
          class="terms text-center"
          data-toggle="modal"
          @click.prevent="termsModal = true"
        >
          {{ __('Read terms') }}
        </div>

        <div class="price">
          {{ offer.price.currency }} {{ offer.price.amount }}
        </div>

        <div>
          <button
            :class="{'active' : selected}"
            class="btn btn-blue"
            @click="$emit('toggle', offer.id)"
          >
            <span v-if="selected">Room added</span>
            <span v-else>{{ __('Add room') }}</span>
          </button>
        </div>
      </div>
    </div>

    <terms-and-conditions-modal
      v-if="termsModal"
      :terms="offer.terms"
      @hide="termsModal = false"
    />
  </div>
</template>

<script>
import TermsAndConditionsModal from '../modals/terms/TermsAndConditionsModal'

export default {
  components: {
    TermsAndConditionsModal
  },

  props: {
    offer: {
      type: Object,
      default: () => {
      }
    },

    index: {
      type: Number,
      required: true
    },

    selected: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      termsModal: false
    }
  },

  computed: {
    offerRooms () {
      if (!this.offer.rooms.length) {
        return []
      }

      return [this.offer.rooms[0]]
    }
  }
}
</script>
