<template>
  <div
    id="trainDetails"
    class="modal fade trip-details"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
  >
    <div
      class="modal-dialog modal-lg"
      role="document"
    >
      <div class="modal-content">
        <div class="modal-header">
          <h5
            id="exampleModalLabel"
            class="modal-title"
          >
            {{ __('Train details') }}
          </h5>

          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="trips oneway-trips">
            <div class="trip train">
              <div class="title mr-5">
                <div class="name text-uppercase">
                  {{ trip.name }}
                </div>
                <div class="company-id small semi-bold">
                  {{ trip.code }}
                </div>
              </div>

              <div class="trip-time-wrap">
                <div class="departure">
                  {{ moment(trip.departure).format('HH:mm') }}
                </div>

                <div class="trip-time">
                  <div>
                    <i class="fa fa-clock-o icon" />
                    <span class="semi-bold">21</span> hr
                    <span class="semi-bold">12</span> min
                  </div>

                  <div
                    v-if="trip.stops"
                    class="stops"
                  >
                    {{ trip.stops }} {{ __('stops') }}
                  </div>
                  <div
                    v-else
                    class="stops"
                  >
                    {{ __('direct') }}
                  </div>
                </div>

                <div class="arrival">
                  {{ moment(trip.arrival).format('HH:mm') }}
                </div>
              </div>

              <div class="flex-fill d-flex justify-content-end">
                <div class="price-selected text-center">
                  <div>$ {{ selectedPrice.price }}</div>
                  <div>{{ __('Base fare') }}</div>
                </div>

                <button class="btn btn-orange text-capitalize">
                  {{ __('Continue Booking') }}
                </button>
              </div>
            </div>
          </div>

          <div class="details">
            <div class="refund non-refund">
              * {{ __('Non Refundable') }}
            </div>

            <div class="details-wrap">
              <div class="details-list">
                <div>
                  <div class="semi-bold mb-2">
                    {{ __('Fare breakup') }}
                  </div>

                  <div class="options">
                    <div class="option-item">
                      <div>{{ __('Adult') }}</div>
                      <div class="ml-3">
                        3
                      </div>
                      <div class="flex-fill text-right semi-bold">
                        $360
                      </div>
                    </div>

                    <div class="option-item">
                      <div>{{ __('Children') }}</div>
                      <div class="ml-3">
                        2
                      </div>
                      <div class="flex-fill text-right semi-bold">
                        $240
                      </div>
                    </div>

                    <div class="option-item">
                      <div>{{ __('Gateway charges') }}</div>
                      <div class="flex-fill text-right semi-bold">
                        $12
                      </div>
                    </div>
                  </div>
                </div>

                <div class="total-estimate">
                  <div>{{ __('Total estimate') }}</div>
                  <div class="total">
                    $ 612
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
  </div>
</template>

<script>

export default {
  props: {
    trip: {
      type: Object,
      default: () => {
      }
    },

    selectedPrice: {
      type: Object,
      required: true
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
    travellersInputText () {
      return this.countTravellers() ? 'Travellers ' + this.countTravellers().toString() : ''
    },

    activitiesTravellersInputText () {
      return this.countActivitiesTravellers() ? this.countActivitiesTravellers().toString() + ' Travellers' : ''
    },

    startTimeInputText () {
      return this.query.startTime ? this.query.startTime : ''
    },

    endTimeInputText () {
      return this.query.endTime ? this.query.endTime : ''
    }
  },

  methods: {
  }
}
</script>
