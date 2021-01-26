<template>
  <div
    id="cancelPlane"
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
            {{ __('Flight details') }}
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
            <div class="trip plane">
              <div class="logo mr-3">
                <img
                  :src="'../images/travel/plane/' + trip.image"
                  alt="logo"
                  class="img-fluid"
                >
              </div>

              <div class="title mr-5">
                <div class="name">
                  {{ trip.name }}
                </div>
                <div class="company-id small semi-bold">
                  {{ trip.code }}
                </div>
              </div>

              <div class=" flex-fill d-flex">
                <div class="trip-label">
                  <div class="departure">
                    <span class="clock">{{ moment(trip.departure).format('HH:mm') }}</span>

                    <span class="small text-muted">
                      <span class="semi-bold">{{ trip.departure_city_code }}</span>
                      {{ trip.departure_city }}, {{ trip.departure_country }} |
                      <span class="semi-bold">{{ moment(trip.departure).format('dd MMM') }}</span>
                    </span>
                  </div>

                  <div class="arrival">
                    {{ moment(trip.arrival).format('HH:mm') }}

                    <span class="small text-muted">
                      <span class="semi-bold">{{ trip.arrival_city_code }}</span>
                      {{ trip.arrival_city }}, {{ trip.arrival_country }} |
                      <span class="semi-bold">{{ moment(trip.arrival).format('dd MMM') }}</span>
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex-fill align-self-start text-center">
                <div class="refund">
                  * {{ __('Refundable') }}
                </div>
              </div>

              <div class="flex-fill d-flex justify-content-end align-self-start">
                <div class="price-selected text-center">
                  <div>$ {{ trip.price.price }}</div>
                </div>
              </div>
            </div>
          </div>

          <div class="details">
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

                <div class="total-estimate cancel">
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

            <div class="btn-wrap">
              <div class="cancel-btn">
                <button class="btn btn-orange btn-block">
                  {{ __('Cancel Booking') }}
                </button>
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
  }
}
</script>
