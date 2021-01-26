<template>
  <div
    id="planeDetails"
    class="modal trip-details"
  >
    <div
      class="modal-dialog modal-lg"
      role="document"
      style="z-index: 99999;"
    >
      <div
        class="modal-content"
        v-on-clickaway="hide"
      >
        <div class="modal-header">
          <h5
            class="modal-title"
          >
            {{ __('Flight details') }}
          </h5>

          <button
            type="button"
            class="close"
            @click.prevent="hide()"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="trips oneway-trips">
            <div class="trip plane">
              <div v-if="airlineLogo" class="logo mr-3">
                <img
                  :src="airlineLogo"
                  alt="logo"
                  class="img-fluid"
                >
              </div>

              <div class="title mr-5">
                <div class="name">
                  {{ trip.route[0].airline }} Airline
                </div>

                <div class="company-id small semi-bold">
                  {{ trip.route[0].id.toUpperCase() }}
                </div>
              </div>

              <div class=" flex-fill d-flex">
                <div class="trip-label">
                  <div class="departure">
                    <span class="clock">{{ moment.unix(trip.dTime).utcOffset('+00:00').format('HH:mm') }}</span>

                    <span class="small text-muted">
                      <span class="semi-bold">{{ trip.cityCodeFrom }}</span>

                      {{ trip.cityFrom }}, {{ trip.countryFrom.name }} |
                      <span class="semi-bold">{{ moment.unix(trip.dTime).utcOffset('+00:00').format('dd MMM') }}</span>
                    </span>
                  </div>

                  <div class="arrival">
                    {{ moment.unix(trip.aTime).utcOffset('+00:00').format('HH:mm') }}

                    <span class="small text-muted">
                      <span class="semi-bold">{{ trip.cityCodeTo }}</span>

                      {{ trip.cityTo }}, {{ trip.countryTo.name }} |
                      <span class="semi-bold">{{ moment.unix(trip.aTime).utcOffset('+00:00').format('dd MMM') }}</span>
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex-fill d-flex justify-content-end">
                <div class="price-selected text-center">
                  <div>{{ query.currency.symbol }}{{ trip.price }}</div>

                  <div>{{ __('Base fare') }}</div>
                </div>

                <button
                  class="btn btn-orange text-capitalize"
                  @click.prevent="hide()"
                >
                  {{ __('Continue Booking') }}
                </button>
              </div>
            </div>
          </div>

          <div class="details">
            <div class="refund">
              * {{ __('Refundable') }}
            </div>

            <div class="details-wrap">
              <div class="details-list">
                <div>
                  <div class="semi-bold mb-2">
                    {{ __('Fare breakup') }}
                  </div>

                  <div class="options">
                    <div class="option-item">
                      <div>{{ __('Adults') }}</div>

                      <div class="ml-3">
                        {{ adultsCount }}
                      </div>

                      <div class="flex-fill text-right semi-bold">
                        {{ query.currency.symbol }}{{ trip.fare.adults * adultsCount }}
                      </div>
                    </div>

                    <div class="option-item">
                      <div>{{ __('Children') }}</div>

                      <div class="ml-3">
                        {{ childrenCount }}
                      </div>

                      <div class="flex-fill text-right semi-bold">
                        {{ query.currency.symbol }}{{ trip.fare.children * childrenCount }}
                      </div>
                    </div>

                    <div class="option-item">
                      <div>{{ __('Infants') }}</div>

                      <div class="ml-3">
                        {{ infantsCount }}
                      </div>

                      <div class="flex-fill text-right semi-bold">
                        {{ query.currency.symbol }}{{ trip.fare.infants * infantsCount }}
                      </div>
                    </div>

                    <div class="option-item">
                      <div>{{ __('Gateway charges') }}</div>

                      <div class="flex-fill text-right semi-bold">
                        {{ query.currency.symbol }}0
                      </div>
                    </div>
                  </div>
                </div>

                <div class="total-estimate">
                  <div>{{ __('Total estimate') }}</div>

                  <div class="total">
                    {{ query.currency.symbol }}{{ Math.floor(totalPrice) }}
                  </div>
                </div>
              </div>

              <div class="term-and-conditions">
                <div class="semi-bold mb-2">
                  {{ __('Terms & conditions') }}
                </div>

                <ul>
                  <li
                    v-for="(cond, index) in termAndConditions"
                    :key="index"
                  >
                    {{ cond }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5;" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    trip: {
      type: Object,
      default: () => {
      }
    },

    travellers: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      termAndConditions: [
        '1st Class',
        'Partially Flexible',
        'Processing fee may apply',
        'Payment in USD',
        'Exchangeable  and refundable with conditions'
      ]
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ]),

    adultsCount () {
      return this.travellers
        .filter(e => {
          return e.type === 'adults'
        })
        .length
    },

    childrenCount () {
      return this.travellers
        .filter(e => {
          return e.type === 'children'
        })
        .length
    },

    infantsCount () {
      return this.travellers
        .filter(e => {
          return e.type === 'infants'
        })
        .length
    },

    totalPrice () {
      return this.trip.fare.adults * this.adultsCount
        + this.trip.fare.children * this.childrenCount
        + this.trip.fare.infants * this.infantsCount
    },

    airlineLogo () {
      return `https://kiwicom-prod.apigee.net/carriers/64/${this.trip.route[0].airline}.png`
    }
  },

  methods: {
    hide () {
      this.$emit('hide')
    }
  }
}
</script>
