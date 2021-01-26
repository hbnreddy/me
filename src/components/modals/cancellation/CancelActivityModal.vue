<template>
  <div
    class="v-modal"
    tabindex="-1"
  >
    <div
      class="modal modal-planner"
    >
      <div
        class="modal-dialog modal-lg"
        role="document"
      >
        <div
          v-on-clickaway="hide"
          class="modal-content"
        >
          <div class="modal-header">
            <h5
              id="exampleModalLabel"
              class="modal-title"
            >
              {{ __('Activities') }} & {{ __('Experiences') }}
            </h5>

            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click.prevent="hide()"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="activity d-flex mb-4">
              <div class="d-flex align-self-center mr-4">
                <i class="fa fa-puzzle-piece icon h4" />
              </div>

              <div class="flex-fill align-self-start">
                <div class="name font-weight-bold">
                  {{ item.name }}
                </div>
                <div class="subtitle">
                  {{ item.name_suffix }}
                </div>
              </div>

              <div class="flex-fill align-self-center">
                <div class="d-flex justify-content-start">
                  <div
                    v-for="(traveller, travellerIndex) in computedTravellers"
                    v-if="travellerIndex < 2"
                    :key="travellerIndex"
                    class="person"
                  >
                    {{ initials(traveller) }}
                  </div>

                  <div
                    v-if="remainingTravellers(computedTravellers)"
                    class="person person-count"
                  >
                    +&nbsp;{{ remainingTravellers(computedTravellers) }}
                  </div>
                </div>
              </div>

              <div class="flex-fill align-self-start text-center">
                <div class="refund px-1">
                  * {{ __('Refundable') }}
                </div>
              </div>

              <div class="flex-fill d-flex justify-content-end">
                <div class="price-selected text-center font-weight-bold">
                  <div>{{ query.currency }} {{ item.price || 0 }}</div>
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
                          {{ adultsCount }}
                        </div>
                        <div class="flex-fill text-right semi-bold">
                          {{ query.currency }} {{ adultsTotalPrices }}
                        </div>
                      </div>

                      <div class="option-item">
                        <div>{{ __('Children') }}</div>
                        <div class="ml-3">
                          {{ childrenCount }}
                        </div>
                        <div class="flex-fill text-right semi-bold">
                          {{ query.currency }} {{ childrenTotalPrices }}
                        </div>
                      </div>

                      <div class="option-item">
                        <div>{{ __('Infants') }}</div>
                        <div class="ml-3">
                          {{ infantsCount }}
                        </div>
                        <div class="flex-fill text-right semi-bold">
                          {{ query.currency }} {{ infantsTotalPrices }}
                        </div>
                      </div>

                      <div class="option-item">
                        <div>{{ __('Gateway charges') }}</div>
                        <div class="flex-fill text-right semi-bold">
                          {{ query.currency }} 0
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="total-estimate cancel">
                    <div>{{ __('Total estimate') }}</div>
                    <div class="total">
                      {{ query.currency }} {{ totalPrice }}
                    </div>
                  </div>
                </div>

                <div class="term-and-conditions">
                  <div class="semi-bold mb-2">
                    {{ __('Terms & conditions') }}
                  </div>

                  <ul class="text-left">
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
                  <button
                    data-dismiss="modal"
                    class="btn btn-orange btn-block"
                    @click.prevent="$emit('cancel')"
                  >
                    {{ __('Cancel Booking') }}
                  </button>
                </div>
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
import {mixin as clickaway} from 'vue-clickaway'
import {mapGetters} from 'vuex'

export default {
  mixins: [
    clickaway
  ],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      items: [],
      travellers: [],
      persons: [
        'AM',
        'HC'
      ],
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
      'currentPlan',
      'query'
    ]),

    computedTravellers () {
      return this.travellers.filter(e => {
        return this.item.travellers.includes(e.id)
      })
    },

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

    adultsTotalPrices () {
      if (! this.item.price) {
        return 0
      }

      return this.item.price.amount * this.adultsCount
    },

    childrenTotalPrices () {
      if (! this.item.price) {
        return 0
      }

      return this.item.price.amount * this.childrenCount
    },

    infantsTotalPrices () {
      if (! this.item.price) {
        return 0
      }

      return this.item.price.amount * this.infantsCount
    },

    totalPrice () {
      return this.infantsTotalPrices
        + this.childrenTotalPrices
        + this.adultsTotalPrices
    }
  },

  created () {
    this.travellers = this.currentPlan.travellers
    this.items = this.currentPlan.items
  },

  methods: {
    hide () {
      this.$emit('hide')
    },

    initials (traveller) {
      if (traveller.first_name && traveller.last_name) {
        return `${traveller.first_name[0]}${traveller.last_name[0]}`
      }

      return traveller.reference_key
    },

    remainingTravellers (travellers) {
      if (travellers.length < 2) {
        return false
      }

      return travellers.length - 2
    }
  }
}
</script>
