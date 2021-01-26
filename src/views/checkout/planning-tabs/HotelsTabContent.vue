<template>
  <div class="stay-tab">
    <div class="box box-border">
      <i class="description-note-stay">{{ __('Click on city to browse stay options.') }}</i>

      <div class="itinerary stay">
        <div
          v-for="(route, index) in computedItinerary"
          :key="index"
          :class="{'active' : selectedCity === index}"
          class="route"
        >
          <div
            class="dot"
            @click.prevent="selectCity(index)"
          >
            <div class="city-code">
              {{ route.city_code }}
            </div>

            <i class="fa fa-circle icon blob grey" />
          </div>

          <div
            v-if="index !== computedItinerary.length - 1"
            class="route-line"
          />
        </div>
      </div>
    </div>

    <!-- filters -->
    <div class="box filter-wrap">
      <hotel-filters v-model="filters" @filter="onFilter()" />

      <div class="position-relative travellers-picker-block">
        <input
          :value="roomsText"
          :class="rooms.length === 0 ? 'blob blue' : ''"
          type="text"
          class="form-control travellers-input"
          placeholder="Guests"
          @click.prevent="roomsPicker = true"
        >

        <rooms-picker
          v-if="roomsPicker"
          v-on-clickaway="hideRoomsPicker"
          :value="rooms"
          @input="onInputRooms($event)"
          @hide="hideRoomsPicker"
        />
      </div>
    </div>

    <div
      id="accordion-hotels"
      class="accordion hotel-list"
    >
      <div v-if="hotels.length || loading">
        <hotel
          v-for="(hotel, hotelIndex) in hotels"
          v-if="!selectedHotel || selectedHotel === hotel.id"
          :key="hotelIndex"
          :search-key="searchKey"
          :hotel="hotel"
          :added-to-plan="isHotelInPlan(hotel.id)"
          :selected="selectedHotel === hotel.id"
          @selected="selectHotel(hotel.id)"
          @store="addToPlan($event)"
          @remove="removeFromPlan($event)"
        />

        <observer
          v-if="offset !== 0 && !selectedHotel"
          style="min-height: 64px;"
          @intersect="onIntersect()"
        />

        <loading-circle
          :loading="loading"
          :small="true"
          :color="'#FD753B'"
          class="my-3"
        />
      </div>

      <div v-else-if="!loading && rooms.length">
        {{ __('Sorry, no hotels available at the moment..') }}
      </div>

      <div v-else>
        {{ __('No rooms selected.') }}
      </div>
    </div>
  </div>
</template>

<script>
import Hotel from '../../../components/hotel/Hotel'
import RoomsPicker from '../../../components/hotel/RoomsPicker'
import LoadingCircle from '../../../components/loading/LoadingCircle'
import Observer from '../../explore/components/Observer'

import {mixin as clickaway} from 'vue-clickaway'
import {mapGetters} from 'vuex'
import {cloneDeep, uniqBy} from 'lodash'
import HotelFilters from '../../../components/hotel/HotelFilters'
import moment from 'moment'
import {HOTEL_ADDED_SUCCESSFULLY} from '../../../bootstrap/notify-messages'

export default {
  components: {
    HotelFilters,
    LoadingCircle,
    Observer,
    RoomsPicker,
    Hotel
  },

  mixins: [
    clickaway
  ],

  data () {
    return {
      fetchedPages: [],
      pollingTime: 60,
      attempts: 3,
      page: 1,
      filters: {
        brands: [],
        facilities: [],
        min_rating: 3
      },
      rooms: [],
      searchKey: false,
      offset: 0,
      loading: false,
      hotels: [],
      selectedHotel: false,
      selectedCity: 0,
      datePicker: false,
      roomsPicker: false,
      activeItem: false,
      cityCode: 'BER'
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'currentPlan',
      'currentTravellers',
      'itinerary'
    ]),

    currentTimeslot () {
      const index = this.itinerary.findIndex(e => e.city_code === this.computedItinerary[this.selectedCity].city_code)

      return {
        startDate: this.itinerary[index].date,
        endDate: this.itinerary[index + 1].date
      }
    },

    computedFilters () {
      let filters = {
        //
      }

      Object.values(['facilities', 'brands'])
        .forEach(key => {
          if (this.filters[key].length) {
            filters[key] = this.filters[key]
              .map(e => e.toLowerCase())
              .join(',')
          }
        })

      return filters
    },

    roomsText () {
      return `Rooms ${this.rooms.length}`
    },

    computedItinerary () {
      let citiesList = cloneDeep(this.itinerary)
      citiesList.shift()
      citiesList.pop()
      citiesList = uniqBy(citiesList, 'city_id')

      return citiesList
    }
  },

  created () {
    this.processTravellers()

    this.fetchHotels()
  },

  methods: {
    async addToPlan (hotel) {
      const timeslot = this.currentTimeslot
      const duration = Math.abs(moment(timeslot.endDate).diff(moment(timeslot.startDate), 'days', true))

      await this.$store.dispatch('storeItem', {
        ...hotel,
        travellers: this.currentTravellers.map(e => e.id),
        timeslot: {
          start_date: '2021-03-03',
          end_date: '2021-03-03'
        },
        city_code: this.computedItinerary[this.selectedCity].city_code,
        date: this.computedItinerary[this.selectedCity].date,
        rooms: this.rooms,
        duration
      })

      this.$notification.show(HOTEL_ADDED_SUCCESSFULLY)
    },

    removeFromPlan (id) {
      this.$store.dispatch('removeItem', id)
    },

    isHotelInPlan (id) {
      return this.currentPlan.items
        .findIndex(e => e.id === id) >= 0
    },

    onIntersect () {
      if (this.loading || this.page === false) {
        return false
      }

      this.page++

      this.fetchHotels()
    },

    onFilter () {
      this.resetResults()
      this.pollingTime = 60

      if (this.rooms.length) {
        this.fetchHotels()
      }
    },

    processTravellers () {
      this.rooms = []

      let adults = this.query.travellers.adults
      let children = this.query.travellers.children
      let infants = this.query.travellers.infants

      let total = adults + children + infants

      if (total <= 4) {
        this.rooms.push({
          adults,
          children,
          infants
        })
      } else {
        while (adults || children || infants) {
          let room = {
            adults: 0,
            children: 0,
            infants: 0
          }

          if (adults > 2 && (children > 2 || infants > 2)) {
            room.adults = 1
            adults--
          } else if (adults > 2) {
            room.adults = 2
            adults -= 2
          } else {
            room.adults = adults
            adults = 0
          }

          if (children > 2) {
            room.children = 2
            children -= 2
          } else {
            room.children = children
            children = 0
          }

          if (infants > 2) {
            room.infants = 2
            infants -= 2
          } else {
            room.infants = infants
            infants = 0
          }

          this.rooms.push(room)
        }
      }

      this.onFilter()
    },

    assignedRoomGuests (room, guests) {
      let assignedGuests = []

      const explode = room.split('.')
      let adults = parseInt(explode[0])
      let children = parseInt(explode[1])
      let infants = parseInt(explode[2])

      while (adults) {
        const index = guests.findIndex(e => {
          return e.type === 'adults'
        })

        assignedGuests.push({
          id: guests[index].id,
          reference_key: guests[index].reference_key,
          active: true,
          type: guests[index].type
        })
        guests.splice(index, 1)

        adults--
      }

      while (children) {
        const index = guests.findIndex(e => {
          return e.type === 'children'
        })

        assignedGuests.push({
          id: guests[index].id,
          reference_key: guests[index].reference_key,
          active: true,
          type: guests[index].type
        })
        guests.splice(index, 1)

        children--
      }

      while (infants) {
        const index = guests.findIndex(e => {
          return e.type === 'infants'
        })

        assignedGuests.push({
          id: guests[index].id,
          reference_key: guests[index].reference_key,
          active: true,
          type: guests[index].type
        })
        guests.splice(index, 1)

        infants--
      }

      return assignedGuests
    },

    async fetchHotels () {
      if (this.fetchedPages.includes(this.page)) {
        return false
      }

      this.loading = true

      let filters = {
        min_stars: this.filters.min_rating,
        page: this.page,
        ...this.computedFilters
      }

      const timeslot = this.currentTimeslot

      const response = await this.$store.dispatch('fetchHotels', {
        city_code: this.computedItinerary[this.selectedCity].city_code,
        date: this.computedItinerary[this.selectedCity].date,
        rooms: this.rooms,
        duration: Math.abs(moment(timeslot.endDate).diff(moment(timeslot.startDate), 'days', true)),
        ...filters
      })

      if (response.polling && this.pollingTime) {
        this.pollingTime -= 3
        setTimeout(this.fetchHotels, 3000)

        return true
      }

      if (!response.entities) {
        return false
      }

      this.fetchedPages.push(this.page)

      this.searchKey = response.search_key
      if (response.entities.length < 10) {
        this.page = false
      }

      this.hotels = [
        ...this.hotels,
        ...response.entities
      ]

      this.offset = this.hotels.length

      this.loading = false
    },

    selectHotel (id) {
      if (this.selectedHotel === id) {
        this.selectedHotel = false
      } else {
        this.selectedHotel = id
      }
    },

    async selectCity (index) {
      if (this.selectedCity === index) {
        return false
      }

      this.resetResults()

      this.selectedCity = index

      await this.fetchHotels()
    },

    onInputRooms (value) {
      this.rooms = value
      this.hideRoomsPicker()

      this.resetResults()

      this.fetchHotels()
    },

    resetResults () {
      this.fetchedPages = []
      this.hotels = []
      this.page = 1
    },

    hideRoomsPicker () {
      this.roomsPicker = false
    }
  }
}
</script>
