<template>
  <div>
    <!-- filters -->
    <div class="box filter-wrap">
      <div class="filter-list">
        <train-hours />
      </div>

      <div class="filters">
        {{ __('Filter by') }}
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

            <train-trip
              v-for="(trip, tripIndex) in route.flights"
              :key="tripIndex"
              :trip="trip"
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
          <train-trip
            v-for="(trip, tripIndex) in onewayTrips"
            :key="tripIndex"
            :trip="trip"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TrainTrip from './TrainTrip'
import TrainHours from '../../../../components/filters/TrainHours'

export default {
  components: {
    TrainHours,
    TrainTrip
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
      roundTrips: [
        {
          departure: 'Hyderabad',
          departure_code: 'HYD',
          arrival: 'Paris',
          arrival_code: 'PAR',
          flights: [
            {
              name: 'Paris Gare Lyon',
              code: '5J1207',
              departure: '2020-01-12 18:50',
              departure_city: 'Paris',
              departure_country: 'France',
              arrival: '2020-01-12 23:02',
              arrival_city: 'Paris',
              arrival_country: 'France',
              stops: 0,
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
              name: 'Paris Gare Lyon',
              code: '5J1207',
              departure: '2020-01-12 18:50',
              departure_city: 'Paris',
              departure_country: 'France',
              arrival: '2020-01-12 23:02',
              arrival_city: 'Paris',
              arrival_country: 'France',
              stops: 0,
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
              name: 'Paris Gare Lyon',
              code: '5J1207',
              departure: '2020-01-12 18:50',
              departure_city: 'Paris',
              departure_country: 'France',
              arrival: '2020-01-12 23:02',
              arrival_city: 'Paris',
              arrival_country: 'France',
              stops: 0,
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
        }
      ],
      onewayTrips: [
        {
          name: 'Paris Gare Lyon',
          code: '5J1207',
          departure: '2020-01-12 18:50',
          departure_city: 'Paris',
          departure_country: 'France',
          arrival: '2020-01-12 23:02',
          arrival_city: 'Paris',
          arrival_country: 'France',
          stops: 0,
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
          name: 'Paris Gare Lyon',
          code: '5J1207',
          departure: '2020-01-12 18:50',
          departure_city: 'Paris',
          departure_country: 'France',
          arrival: '2020-01-12 23:02',
          arrival_city: 'Paris',
          arrival_country: 'France',
          stops: 0,
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
          name: 'Paris Gare Lyon',
          code: '5J1207',
          departure: '2020-01-12 18:50',
          departure_city: 'Paris',
          departure_country: 'France',
          arrival: '2020-01-12 23:02',
          arrival_city: 'Paris',
          arrival_country: 'France',
          stops: 0,
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
          name: 'Paris Gare Lyon',
          code: '5J1207',
          departure: '2020-01-12 18:50',
          departure_city: 'Paris',
          departure_country: 'France',
          arrival: '2020-01-12 23:02',
          arrival_city: 'Paris',
          arrival_country: 'France',
          stops: 0,
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
      ]
    }
  },

  methods: {
    onSelectPriceItem (index) {
      this.selectedPriceItem = index
    }
  }
}
</script>
