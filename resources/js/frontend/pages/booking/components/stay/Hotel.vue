<template>
  <div
    :class="{'active' : !selected || selected}"
    class="box box-border hotel"
  >
    <div class="hotel-header">
      <div class="image">
        <img
          :src="'../images/stay/' + hotel.image"
          alt="Hotel image"
          class="img-fluid"
        >
      </div>

      <div class="options">
        <div class="d-flex justify-content-between mb-2">
          <div class="mb-1">
            <div class="name">
              {{ hotel.name }}
            </div>
            <div class="subtitle">
              {{ hotel.subtitle }}
            </div>
          </div>

          <div>
            <div class="price">
              <i class="fa fa-bed icon" />
              $ {{ hotel.price }}
            </div>

            <div class="text-lowercase text-muted small">
              {{ __('for') }} {{ hotel.priceFor }}
            </div>
          </div>
        </div>

        <div class="rating mb-2">
          <i class="fa fa-star icon" />
          <span class="rating-badge">{{ hotel.rate }}</span>
        </div>

        <div class="nearby mb-3">
          {{ hotel.nearby.distance }} {{ __('km from') }} {{ hotel.nearby.place }}
        </div>

        <div class="facilities">
          <div
            v-for="(facility, index) in hotel.facilities"
            :key="index"
            class="facility"
          >
            <i
              :class="'fa-' + facility.icon"
              class="icon fa"
            />
            {{ facility.name }}
          </div>
        </div>

        <div class="d-flex align-items-end justify-content-between">
          <div class="small text-muted">
            {{ __('Check T&C before adding to plan') }}
          </div>

          <div>
            <button
              :data-target="`#hotel${hotel.id}`"
              :aria-controls="`hotel${hotel.id}`"
              :class="{active: selected === true}"
              class="btn btn-orange btn-select-hotel text-capitalize"
              data-toggle="collapse"
              aria-expanded="false"
              @click.prevent="$emit('selected')"
            >
              <span v-if="selected">
                {{ __('Modify selection') }}
                <i class="fa fa-angle-up pl-2" />
              </span>

              <span v-else>{{ __('Choose rooms') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      :id="`hotel${hotel.id}`"
      data-parent="#accordion-hotels"
      class="collapse"
    >
      <div class="hotel-body">
        <div
          id="accordionExample"
          class="accordion"
        >
          <room-details
            v-for="(room, index) in hotel.rooms"
            :key="index"
            :room="room"
            :hotel-id="hotel.id"
            :room-id="index"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import RoomDetails from './RoomDetails'

export default {
  components: {
    RoomDetails
  },

  props: {
    hotel: {
      type: Object,
      default: () => {
      }
    },

    selected: {
      type: Boolean,
      default: false
    }
  },

  methods: {
    //
  }
}
</script>
