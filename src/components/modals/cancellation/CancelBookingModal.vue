<template>
  <div
    id="cancelBooking"
    class="modal fade cancel-booking"
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
            {{ __('Hotel booking') }}
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
          <div class="hotel">
            <div class="hotel-header d-flex">
              <div class="d-flex mr-2">
                <div class="small-col">
                  <i class="fa fa-bed icon-hotel" />

                  <div class="rating mb-2">
                    <i class="fa fa-star icon" />
                    <span class="rating-badge">4.3</span>
                  </div>
                </div>

                <div>
                  <div>
                    <div class="name">
                      Hotel Banville
                    </div>
                    <div class="subtitle">
                      Arc de Triumph
                    </div>
                  </div>

                  <div>Paris</div>
                </div>
              </div>

              <div class="room-list">
                <div class="room-wrap d-flex justify-content-between">
                  <div class="name px-1">
                    {{ __('Room') }} 1
                  </div>
                  <div class="type px-1">
                    {{ __('Single') }}
                  </div>
                  <div class="pack px-1">
                    {{ __('Superior') }}
                  </div>

                  <div class="guests px-1">
                    <i class="fa fa-users icon" />
                    <span class="badge small">4</span>
                  </div>

                  <div class="terms px-1">
                    {{ __('Read terms') }}
                  </div>

                  <div class="refund px-1">
                    * {{ __('Refundable') }}
                  </div>

                  <div class="price px-1">
                    $ 350
                  </div>
                </div>

                <div class="room-wrap d-flex justify-content-between">
                  <div class="name px-1">
                    {{ __('Room') }} 1
                  </div>
                  <div class="type px-1">
                    {{ __('Single') }}
                  </div>
                  <div class="pack px-1">
                    {{ __('Superior') }}
                  </div>

                  <div class="guests px-1">
                    <i class="fa fa-users icon" />
                    <span class="badge small">4</span>
                  </div>

                  <div
                    class="terms px-1"
                    data-toggle="modal"
                    data-target="#termsAndConditions"
                  >
                    {{ __('Read terms') }}
                  </div>

                  <div class="refund px-1">
                    * {{ __('Refundable') }}
                  </div>

                  <div class="price px-1">
                    $ 350
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="details">
            <div class="details-wrap">
              <div class="details-list">
                <div>
                  <div class="semi-bold mb-2">
                    {{ __('Fare break') }}
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
    activity: {
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
  },

  created () {
    //
  },

  methods: {
  }
}
</script>
