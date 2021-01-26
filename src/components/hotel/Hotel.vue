<template>
  <div
    :class="{'active' : isActive}"
    class="box box-border hotel"
  >
    <div class="hotel-header">
      <div class="hotel-image-block">
        <img
          :src="hotel.image_url"
          alt="Hotel image"
          class="img-fluid hotel-image"
        >
      </div>

      <div class="options left-side">
        <div class="top">
          <div class="d-flex justify-content-between mb-2">
            <div class="mb-1">
              <div class="name">
                {{ hotel.name }}
              </div>

              <div class="subtitle">
                {{ hotel.address }}
              </div>
            </div>
          </div>

          <div class="rating mb-2">
            <i class="fa fa-star icon" />
            <span class="rating-badge">{{ parseInt(hotel.stars_rating).toFixed(1) }}</span>
          </div>

          <div v-if="hotel.nearby" class="nearby mb-3">
            {{ hotel.nearby.distance }} {{ __('km from') }} {{ hotel.nearby.place }}
          </div>

          <div class="facilities">
            <div
              v-for="(facility, index) in hotelFacilities"
              :key="index"
              class="facility"
            >
              <i
                :class="'fa-' + facilityIcon(facility.type)"
                class="icon fa"
              />
              {{ facility.details }}
            </div>
          </div>

          <div class="facilities-options">
            <a
              v-if="hotel.facilities.length > maxFacilities && !showAllFacilities"
              class="show-more text-decoration-none"
              href="#"
              @click.prevent="showAllFacilities = true"
            >
              Show more
            </a>

            <a
              v-if="hotel.facilities.length > maxFacilities && showAllFacilities"
              class="show-more text-decoration-none"
              href="#"
              @click.prevent="showAllFacilities = false"
            >
              Show less
            </a>
          </div>
        </div>

        <div class="bottom">
          <div class="d-flex align-items-end justify-content-between">
            <div class="small text-muted">
              {{ __('Check T&C before adding to plan') }}
            </div>
          </div>
        </div>
      </div>

      <div class="right-side">
        <div class="price">
          <p v-if="hotel.price.average" class="amount">
            <i class="fa fa-bed icon" />
            ${{ hotel.price.average }}

            <span v-if="hotel.price.min" class="min-price">
              (min. ${{ hotel.price.min }})
            </span>
          </p>

          <p v-else>
            No price
          </p>
        </div>

        <div class="d-flex flex-column">
          <button
            :class="{active: isActive}"
            class="btn btn-orange btn-select-hotel text-capitalize mb-1"
            @click.prevent="$emit('selected')"
          >
            <span v-if="isActive">
              {{ __('Modify selection') }}
              <i class="fa fa-angle-up pl-2" />
            </span>

            <span v-else>{{ __('Choose rooms') }}</span>
          </button>

          <button
            v-if="addedToPlan"
            class="btn btn-orange btn-select-hotel text-capitalize"
            @click.prevent="$emit('remove', hotel.id)"
          >
            <span>{{ __('Remove from plan') }}</span>
          </button>

          <button
            v-else
            :class="{ 'btn-checkout btn-disabled': false }"
            class="btn btn-orange btn-select-hotel text-capitalize"
            @click.prevent="addToPlan()"
          >
            <span>{{ __('Add to plan') }}</span>
          </button>
        </div>
      </div>
    </div>

    <div v-if="selected">
      <div class="hotel-body">
        <loading-circle
          :loading="loading"
          :small="true"
          :color="'#FD753B'"
          class="my-3"
        />

        <room-details
          v-for="(offer, index) in offers"
          :key="index"
          :offer="offer"
          :index="index"
          :selected="selectedOffers.includes(offer.id)"
          @toggle="toggleOffer($event)"
        />
      </div>
    </div>
  </div>
</template>

<script>
import RoomDetails from './RoomDetails'
import LoadingCircle from '../loading/LoadingCircle'

import { mapGetters } from 'vuex'
import {NO_ROOMS_SELECTED} from '../../bootstrap/notify-messages'

export default {
  components: {
    LoadingCircle,
    RoomDetails
  },

  props: {
    hotel: {
      type: Object,
      default: () => {
      }
    },

    searchKey: {
      type: String,
      required: true
    },

    selected: {
      type: Boolean,
      default: false
    },

    addedToPlan: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      loading: false,
      offers: [],
      selectedOffers: [],
      maxFacilities: 8,
      showAllFacilities: false
    }
  },

  computed: {
    ...mapGetters([
      'facilities'
    ]),

    canAddToPlan () {
      return true
    },

    isActive () {
      return this.addedToPlan || this.selected
    },

    hotelFacilities () {
      if (this.showAllFacilities) {
        return this.hotel.facilities
      }

      return this.hotel.facilities.slice(0, this.maxFacilities)
    }
  },

  watch: {
    selected: {
      handler () {
        if (this.selected) {
          this.fetchHotelDetails()
        }
      }
    }
  },

  methods: {
    addToPlan () {
      if (!this.selectedOffers.length) {
        this.$notification.show(NO_ROOMS_SELECTED)

        return false
      }

      this.$emit('store', {
        id: this.hotel.id,
        code: this.hotel.code,
        search_key: this.searchKey,
        name: this.hotel.name,
        type: 'hotel',
        offers: this.hotel.offers.filter(e => {
          return this.selectedOffers.includes(e.id)
        }).map(offer => {
          return {
            offer_id: offer.id,
            price: offer.price
          }
        }),
        image_url: this.hotel.image_url
      })
    },

    toggleOffer (offerId) {
      if (this.selectedOffers.includes(offerId)) {
        const index = this.selectedOffers.findIndex(e => e === offerId)
        this.selectedOffers.splice(index, 1)
      } else {
        this.selectedOffers.push(offerId)
      }
    },

    async fetchHotelDetails () {
      this.loading = true
      this.offers = []

      this.offers = await this.$store.dispatch('fetchHotelOffers', {
        id: this.hotel.id,
        searchKey: this.searchKey
      })

      this.loading = false
    },

    facilityIcon (type) {
      const facility = this.facilities.find(e => {
        return e.type === type
      })

      return facility ? facility.icon : ''
    }
  }
}
</script>
