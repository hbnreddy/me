<template>
  <div class="card shadow-sm">
    <div
      :id="'heading' + hotelId + roomId"
      :data-target="'#collapse' + hotelId + roomId"
      :aria-controls="'collapse' + hotelId + roomId"
      data-toggle="collapse"
      aria-expanded="false"
      class="card-header"
    >
      <div class="room-wrap d-flex justify-content-between">
        <div class="name">
          {{ room.name }}
        </div>
        <div class="type">
          {{ room.type }}
        </div>
        <div class="guests">
          {{ __('Guests') }}
          <i class="fa fa-users icon" />
          <span class="badge small">{{ room.guests }}</span>
        </div>

        <div class="pack">
          <span>{{ __('Superior') }}</span>
        </div>

        <div class="toggle-icon">
          <i class="fa fa-angle-down" />
        </div>
      </div>
    </div>

    <div
      :id="'collapse' + hotelId + roomId"
      :aria-labelledby="'heading' + hotelId + roomId"
      class="collapse"
    >
      <div class="card-body">
        <div
          v-for="(pack, packIndex) in room.packages"
          :key="packIndex"
          class="room-wrap"
        >
          <div class="type">
            {{ pack.type }}
          </div>
          <div class="country">
            {{ pack.countryCode }}
          </div>
          <div
            :class="{'success' : pack.refundable}"
            class="refundable"
          >
            <span v-if="!pack.refundable">Non</span>
            {{ __('refundable') }}
          </div>

          <div
            class="terms"
            data-toggle="modal"
            @click.prevent="termsModal = true"
          >
            {{ __('Read terms') }}
          </div>

          <div class="price">
            $ {{ pack.price }}
          </div>

          <div>
            <button
              :class="{'active' : selectedPack === packIndex}"
              class="btn btn-blue"
              @click="selectPack(packIndex)"
            >
              <span v-if="selectedPack === packIndex">Room added</span>
              <span v-else>{{ __('Add to plan') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <terms-and-conditions-modal v-if="termsModal" />
  </div>
</template>

<script>
import TermsAndConditionsModal from '../../../../modals/TermsAndConditionsModal'

export default {
  components: {
    TermsAndConditionsModal
  },

  props: {
    room: {
      type: Object,
      default: () => {
      }
    },

    hotelId: {
      type: Number,
      required: true
    },

    roomId: {
      type: Number,
      required: true
    }
  },

  data () {
    return {
      termsModal: false,
      selectedPack: false
    }
  },

  methods: {
    selectPack (index) {
      this.selectedPack = index
    }
  }
}
</script>
