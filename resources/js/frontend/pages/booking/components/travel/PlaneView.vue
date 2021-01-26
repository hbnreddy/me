<template>
  <div>
    <!-- filters -->
    <div class="box filter-wrap">
      <div class="filter-list">
        <stops />

        <bags />

        <price />

        <airlines />

        <hours />
      </div>

      <div class="filters">
        Filter by
        <i class="fa fa-filter mx-2" />
      </div>
    </div>

    <!-- Trips -->
    <div class="trips-wrap">
      <div
        v-if="routeType === $const('ROUTE_TYPE_ROUND')"
        class="box box-border trips round-trips"
      >
        <div class="heading">
          <div class="semi-bold text-capitalize">
            {{ __('Round trip') }}
          </div>

          <div>{{ __('Duration') }} <span class="semi-bold">38</span> hrs <span class="semi-bold">25</span> min</div>

          <div>
            <span class="old-price">$4000</span>
            <span class="new-price">$3980</span>

            <button class="btn btn-orange">
              {{ __('Add to plan') }}
            </button>
          </div>
        </div>

        <div class="body">
          <div
            v-for="(route, routeIndex) in roundTrips"
            :key="routeIndex"
            class="trip-content"
          >
            <nav
              class="breadcrumbs"
              aria-label="breadcrumb"
            >
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  {{ route.departure_code }}
                </li>
                <li class="breadcrumb-item">
                  {{ route.arrival_code }}
                </li>
              </ol>
            </nav>

            <plane-trip
              v-for="(trip, tripIndex) in route.flights"
              :key="tripIndex"
              :trip="trip"
              :chosen="trip.id === chosenFlightId"
              @chosen="chooseFlight($event)"
            />
          </div>
        </div>
      </div>

      <div
        v-else
        class="box box-border trips oneway-trips"
      >
        <div class="heading">
          <div class="semi-bold text-capitalize">
            {{ __('Oneway') }}
          </div>

          <button class="btn btn-orange">
            {{ __('Add to plan') }}
          </button>
        </div>

        <div class="body">
          <plane-trip
            v-for="(trip, tripIndex) in onewayTrips"
            :key="tripIndex"
            :trip="trip"
            :chosen="trip.id === chosenFlightId"
            @chosen="chooseFlight($event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import PlaneTrip from './PlaneTrip'
import Stops from '../../../../components/filters/Stops'
import Bags from '../../../../components/filters/Bags'
import Price from '../../../../components/filters/Price'
import Airlines from '../../../../components/filters/Airlines'
import Hours from '../../../../components/filters/Hours'

export default {
  components: {
    Hours,
    Airlines,
    Price,
    Bags,
    Stops,
    PlaneTrip
  },

  props: {
    routeType: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      selectedPriceItem: 0,
      chosenFlightId: false,
      roundTrips: [
        {
          departure: 'Hyderabad',
          departure_code: 'HYD',
          arrival: 'Paris',
          arrival_code: 'PAR',
          flights: [
            {
              id: 1,
              image: 'plane1.jpg',
              name: 'IndiGo',
              code: '0E349820',
              departure: '2019-05-27 12:20',
              departure_city: 'Hyderabad',
              departure_code: 'HYD',
              departure_country: 'India',
              arrival: '2019-05-26 21:20',
              arrival_city: 'Paris',
              arrival_code: 'PAR',
              arrival_country: 'France',
              stops: 0,
              checked: false,
              trip_type_code: 'R',
              priceList: [
                {
                  price: '120',
                  package: 'light'
                },
                {
                  price: '100',
                  package: 'standard'
                },
                {
                  price: '100',
                  package: 'flex'
                }
              ]
            }
          ]
        },
        {
          departure: 'Paris',
          departure_code: 'PAR',
          arrival: 'Hyderabad',
          arrival_code: 'HYD',
          flights: [
            {
              id: 2,
              image: 'plane1.jpg',
              name: 'Spice Jet',
              code: '0E349820',
              departure: '20190-05-12 12:20',
              departure_city: 'Hyderabad',
              departure_code: 'HYD',
              departure_country: 'India',
              arrival: '2019-05-13 21:30',
              arrival_city: 'Paris',
              arrival_code: 'PAR',
              arrival_country: 'France',
              stops: 2,
              checked: true,
              trip_type_code: 'NR',
              priceList: [
                {
                  price: '120',
                  package: 'light'
                },
                {
                  price: '100',
                  package: 'standard'
                },
                {
                  price: '100',
                  package: 'flex'
                }
              ]
            },
            {
              id: 3,
              image: 'plane1.jpg',
              name: 'IndiGo',
              code: '0E349820',
              departure: '2019-01-01 12:20',
              departure_city: 'Hyderabad',
              departure_code: 'HYD',
              departure_country: 'India',
              arrival: '2019-01-02 21:30',
              arrival_city: 'Paris',
              arrival_code: 'PAR',
              arrival_country: 'France',
              stops: 2,
              checked: false,
              trip_type_code: 'NR',
              priceList: [
                {
                  price: '120',
                  package: 'light'
                },
                {
                  price: '100',
                  package: 'standard'
                },
                {
                  price: '100',
                  package: 'flex'
                }
              ]
            }
          ]
        }
      ],
      onewayTrips: [
        {
          id: 4,
          image: 'plane1.jpg',
          name: 'IndiGo',
          code: '0E349820',
          departure: '2019-05-27 12:20',
          departure_city: 'Hyderabad',
          departure_code: 'HYD',
          departure_country: 'India',
          arrival: '2019-05-26 21:20',
          arrival_city: 'Paris',
          arrival_code: 'PAR',
          arrival_country: 'France',
          stops: 0,
          checked: false,
          trip_type_code: 'R',
          priceList: [
            {
              price: '120',
              package: 'light'
            },
            {
              price: '100',
              package: 'standard'
            },
            {
              price: '100',
              package: 'flex'
            }
          ]
        },
        {
          id: 5,
          image: 'plane1.jpg',
          name: 'IndiGo',
          code: '0E349820',
          departure: '2019-05-27 12:20',
          departure_city: 'Hyderabad',
          departure_code: 'HYD',
          departure_country: 'India',
          arrival: '2019-05-26 21:20',
          arrival_city: 'Paris',
          arrival_code: 'PAR',
          arrival_country: 'France',
          stops: 0,
          checked: false,
          trip_type_code: 'R',
          priceList: [
            {
              price: '120',
              package: 'light'
            },
            {
              price: '100',
              package: 'standard'
            },
            {
              price: '100',
              package: 'flex'
            }
          ]
        },
        {
          id: 6,
          image: 'plane1.jpg',
          name: 'IndiGo',
          code: '0E349820',
          departure: '2019-05-27 12:20',
          departure_city: 'Hyderabad',
          departure_code: 'HYD',
          departure_country: 'India',
          arrival: '2019-05-26 21:20',
          arrival_city: 'Paris',
          arrival_code: 'PAR',
          arrival_country: 'France',
          stops: 0,
          checked: false,
          trip_type_code: 'NR',
          priceList: [
            {
              price: '120',
              package: 'light'
            },
            {
              price: '100',
              package: 'standard'
            },
            {
              price: '100',
              package: 'flex'
            }
          ]
        },
        {
          id: 7,
          image: 'plane1.jpg',
          name: 'IndiGo',
          code: '0E349820',
          departure: '2019-05-27 12:20',
          departure_city: 'Hyderabad',
          departure_code: 'HYD',
          departure_country: 'India',
          arrival: '2019-05-26 21:20',
          arrival_city: 'Paris',
          arrival_code: 'PAR',
          arrival_country: 'France',
          stops: 0,
          checked: false,
          trip_type_code: 'R',
          priceList: [
            {
              price: '120',
              package: 'light'
            },
            {
              price: '100',
              package: 'standard'
            },
            {
              price: '100',
              package: 'flex'
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
        },
        {
          title: 'price',
          options: [
            'Action 1',
            'Action 2',
            'Action 3'
          ]
        },
        {
          title: 'bags',
          options: [
            'Action 1',
            'Action 2',
            'Action 3'
          ]
        },
        {
          title: 'airlines',
          options: [
            'Action 1',
            'Action 2',
            'Action 3'
          ]
        }
      ]
    }
  },

  methods: {
    chooseFlight (id) {
      this.chosenFlightId = id
    },

    onSelectPriceItem (index) {
      this.selectedPriceItem = index
    }
  }
}
</script>
