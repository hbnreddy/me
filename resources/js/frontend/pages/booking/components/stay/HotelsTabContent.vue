<template>
  <div>
    <div class="box box-border">
      <i class="description-note-stay">{{ __('Click on city to browse stay options.') }}</i>
      <div class="itinerary stay">
        <div
          v-for="(route, index) in itinerary.routes"
          :key="index"
          :class="{'active' : selectedRoute === index}"
          class="route"
        >
          <div
            class="dot"
            @click="selectRoute(index)"
          >
            <div class="city-code">
              {{ route.code }}
            </div>
            <i class="fa fa-circle icon blob grey" />
          </div>

          <div
            v-if="index !== itinerary.routes.length - 1"
            class="route-line"
          />
        </div>
      </div>

      <div class="d-flex">
        <div class="form-group position-relative mx-2">
          <input
            id="location2"
            :value="originCityValue"
            type="text"
            class="form-control mr-4"
            aria-describedby="location"
            placeholder="Origin city"
            @input="searchOriginPlace($event)"
          >

          <div
            v-if="showResultsDropdown && searchResults.length"
            class="dropdown-results"
          >
            <ul class="results-list">
              <li
                v-for="(result, index) in searchResults"
                :key="index"
                class="results-list-item"
              >
                <a
                  href="#"
                  @click.prevent="selectPlace(index)"
                >
                  {{ result.name }}, {{ result.name_suffix }}
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div class="form-group position-relative mx-2">
          <input
            :value="datePickerInputText"
            type="text"
            class="form-control mr-4"
            placeholder="A week in Month"
            @click.prevent="showDatePicked()"
          >

          <extended-date-picker
            v-if="datePicker"
            v-on-clickaway="onClickOutsideDatePicker"
            :value="actualQuery.date"
            :multiple-values="false"
            @input="onDatePickerInput($event)"
            @hide="hideDatePicker"
          />
        </div>

        <div class="form-group position-relative mx-2">
          <input
            :value="guestInputText"
            type="text"
            class="form-control mr-4"
            placeholder="A week in Month"
            @click.prevent="showGuestPicked()"
          >

          <guests-picker
            v-if="guestPicker"
            v-on-clickaway="onClickOutsideGuestPicker"
            :value="actualQuery.guests"
            :multiple-values="false"
            @input="onGuestPickerInput($event)"
            @hide="hideGuestPicker"
          />
        </div>
      </div>
    </div>

    <!-- filters -->
    <div class="box filter-wrap">
      <div class="filter-list">
        <brands />

        <facilities />

        <guest-rating />
      </div>

      <div class="filters">
        {{ __('Filter by') }}
        <i class="fa fa-filter mx-2" />
      </div>
    </div>

    <div
      id="accordion-hotels"
      class="accordion hotel-list"
    >
      <hotel
        v-for="(hotel, hotelIndex) in hotelList"
        v-if="!selectedHotel || selectedHotel === hotel.id"
        :key="hotelIndex"
        :hotel="hotel"
        :selected="selectedHotel === hotel.id"
        @selected="selectHotel(hotel.id)"
      />
    </div>
  </div>
</template>

<script>
import ExtendedDatePicker from '../../../../components/TravelDatePicker'
import axios from 'axios'
import {mixin as clickaway} from 'vue-clickaway'
import {cloneDeep} from 'lodash'
import {mapGetters} from 'vuex'
import * as moment from 'moment'
import Hotel from './Hotel'
import Brands from '../../../../components/filters/Brands'
import Facilities from '../../../../components/filters/Facilities'
import GuestRating from '../../../../components/filters/GuestRating'
import GuestsPicker from '../../../../components/GuestsPicker'

export default {

  components: {
    GuestsPicker,
    GuestRating,
    Facilities,
    Brands,
    Hotel,
    ExtendedDatePicker
  },
  mixins: [
    clickaway
  ],

  props: {
    trip: {
      type: Object,
      default: () => {
      }
    }
  },

  data () {
    return {
      selectedHotel: false,
      selectedRoute: 0,
      showResultsDropdown: false,
      searchResults: [],
      datePicker: false,
      guestPicker: false,
      actualQuery: {
        //
      },
      activeItem: false,
      hotelList: [
        {
          id: 1,
          image: 'hotel-room.jpg',
          name: 'Hotel Banville',
          subtitle: 'Arch de triumph',
          price: '100',
          priceFor: 'two rooms',
          rate: 4.5,
          nearby: {
            place: 'Eiffel Tower',
            distance: 1.2
          },
          facilities: [
            {
              icon: 'fire',
              name: 'Restaurant'
            },
            {
              icon: 'fire',
              name: 'Private pool'
            },
            {
              icon: 'fire',
              name: 'Fire place'
            }
          ],
          rooms: [
            {
              name: 'Room 1',
              type: 'single',
              guests: 2,
              packages: [
                {
                  type: 'deluxe',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                },
                {
                  type: 'superior',
                  countryCode: 'RO',
                  refundable: true,
                  price: '60'
                },
                {
                  type: 'premium',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                }
              ]
            },
            {
              name: 'Room 2',
              type: 'double',
              guests: 2,
              packages: [
                {
                  type: 'deluxe',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                },
                {
                  type: 'superior',
                  countryCode: 'RO',
                  refundable: true,
                  price: '60'
                },
                {
                  type: 'premium',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                }
              ]
            }
          ]
        },
        {
          id: 2,
          image: 'hotel-room.jpg',
          name: 'Hotel Banville',
          subtitle: 'Arch de triumph',
          price: '100',
          priceFor: 'two rooms',
          rate: 4.5,
          nearby: {
            place: 'Eiffel Tower',
            distance: 1.2
          },
          facilities: [
            {
              icon: 'fire',
              name: 'Restaurant'
            },
            {
              icon: 'fire',
              name: 'Private pool'
            },
            {
              icon: 'fire',
              name: 'Fire place'
            },
            {
              icon: 'fire',
              name: 'Spa(unisex)'
            }
          ],
          rooms: [
            {
              name: 'Room 1',
              type: 'single',
              guests: 2,
              packages: [
                {
                  type: 'deluxe',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                },
                {
                  type: 'superior',
                  countryCode: 'RO',
                  refundable: true,
                  price: '60'
                },
                {
                  type: 'premium',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                }
              ]
            },
            {
              name: 'Room 2',
              type: 'double',
              guests: 2,
              packages: [
                {
                  type: 'deluxe',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                },
                {
                  type: 'superior',
                  countryCode: 'RO',
                  refundable: true,
                  price: '60'
                },
                {
                  type: 'premium',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                }
              ]
            }
          ]
        },
        {
          id: 3,
          image: 'hotel-room.jpg',
          name: 'Hotel Banville',
          subtitle: 'Arch de triumph',
          price: '100',
          priceFor: 'two rooms',
          rate: 4.5,
          nearby: {
            place: 'Eiffel Tower',
            distance: 1.2
          },
          facilities: [
            {
              icon: 'fire',
              name: 'Restaurant'
            },
            {
              icon: 'fire',
              name: 'Private pool'
            },
            {
              icon: 'fire',
              name: 'Fire place'
            },
            {
              icon: 'fire',
              name: 'Spa(unisex)'
            }
          ],
          rooms: [
            {
              name: 'Room 1',
              type: 'single',
              guests: 2,
              packages: [
                {
                  type: 'deluxe',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                },
                {
                  type: 'superior',
                  countryCode: 'RO',
                  refundable: true,
                  price: '60'
                },
                {
                  type: 'premium',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                }
              ]
            },
            {
              name: 'Room 2',
              type: 'double',
              guests: 2,
              packages: [
                {
                  type: 'deluxe',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                },
                {
                  type: 'superior',
                  countryCode: 'RO',
                  refundable: true,
                  price: '60'
                },
                {
                  type: 'premium',
                  countryCode: 'RO',
                  refundable: false,
                  price: '60'
                }
              ]
            }
          ]
        }
      ],
      filters: [
        {
          title: 'stops',
          options: [
            'Action 1',
            'Action 2',
            'Action 3'
          ]
        },
        {
          title: 'time',
          options: [
            'Action 1',
            'Action 2',
            'Action 3'
          ]
        }
      ],
      itinerary: {
        routes: [
          {
            city: 'Hyderabad',
            code: 'HYD'
          },
          {
            city: 'Paris',
            code: 'PAR'
          },
          {
            city: 'Zurich',
            code: 'ZUR'
          },
          {
            city: 'London',
            code: 'LON'
          },
          {
            city: 'Hyderabad',
            code: 'HYD'
          }
        ]
      }
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ]),

    originCityValue () {
      if (!this.actualQuery.origin_city) {
        return false
      }

      return this.actualQuery.origin_city.full_name
          || this.actualQuery.origin_city.name + ', ' + this.actualQuery.origin_city.name_suffix
    },

    datePickerInputText () {
      return this.actualQuery.date.date_string
    },

    guestInputText () {
      return `Guests ${this.actualQuery.guests}`
    }
  },

  created () {
    this.actualQuery = cloneDeep(this.query)

    if (!this.actualQuery.date.date_string) {
      let date = this.actualQuery.date
      let dateString = ''
      if (date.type === this.$const('DATE_TYPE_EXACT') && date.start_date && date.end_date) {
        dateString = moment(date.start_date).format(this.$const('PICKER_DATE_FORMAT'))
            + ' - '
            + moment(date.end).format(this.$const('PICKER_DATE_FORMAT'))
      } else if (date.type === this.$const('DATE_TYPE_FLEXIBLE')) {
        const flexibleType = this.$const('TRIP_TYPES').find(e => {
          return e.value === date.flexible_type
        })

        dateString = date.month
            + ' '
            + date.year
            + ' - '
            + flexibleType.text
      }

      this.actualQuery.date.date_string = dateString
    }

    if (!this.actualQuery.guests) {
      this.actualQuery.guests = this.actualQuery.travellers.adults
          + this.actualQuery.travellers.children
          + this.actualQuery.travellers.infants
    }
  },

  methods: {
    selectHotel (id) {
      if (this.selectedHotel === id) {
        this.selectedHotel = false
      } else {
        this.selectedHotel = id
      }
    },

    updateQuery () {
      this.$store.commit('setQuery', cloneDeep(this.actualQuery))
    },

    selectPlace (index) {
      this.actualQuery.originCity = this.searchResults[index]
      this.actualQuery.originCity.full_name = this.actualQuery.origin_city.name + ', ' + this.actualQuery.originCity.name_suffix
      this.showResultsDropdown = false
    },

    onDatePickerInput (value) {
      this.actualQuery.date = cloneDeep(value)

      this.updateQuery()
    },

    onClickOutsideDatePicker () {
      this.datePicker = false
    },

    showDatePicked () {
      this.datePicker = true
    },

    hideDatePicker () {
      this.datePicker = false
    },

    searchOriginPlace (evt) {
      const searchValue = evt.target.value
      if (!searchValue || searchValue === '') {
        return false
      }

      axios
        .get('/places/search', {
          params: {
            query: searchValue,
            level: 'city'
          }
        })
        .then(r => {
          if (r.data.success) {
            this.searchResults = r.data.places
          }
        })

      this.showResultsDropdown = true
    },

    selectRoute (index) {
      this.selectedRoute = index
    },

    onGuestPickerInput (value) {
      this.actualQuery.guests = cloneDeep(value)

      this.updateQuery()
    },

    showGuestPicked () {
      this.guestPicker = true
    },

    onClickOutsideGuestPicker () {
      this.guestPicker = false
    },

    hideGuestPicker () {
      this.guestPicker = false
    }
  }
}
</script>
