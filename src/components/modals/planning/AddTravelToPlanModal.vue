<template>
  <div
    class="modal trip-details"
  >
    <div
      class="modal-dialog modal-lg"
      role="document"
      style="z-index: 99999;"
    >
      <div
        v-on-clickaway="hide"
        class="modal-content"
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
            <trip-row
              v-for="ride in items"
              :key="ride.id"
              :trip="ride"
              class="trip plane"
            />
          </div>

          <div class="details">
            <div v-if="1 === 0" class="refund">
              * {{ __('Refundable') }}
            </div>

            <div class="details-wrap">
              <div v-if="1 === 1" class="details-list">
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

                <div class="total-estimate">
                  <div>{{ __('Total estimate') }}</div>

                  <div class="total">
                    {{ query.currency }} {{ Math.floor(totalPrice) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button
              class="btn btn-orange text-capitalize"
              @click.prevent="hide()"
            >
              {{ __('Continue Booking') }}
            </button>
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
import TripRow from '../../../components/travel/TripRow'

export default {
  components: {TripRow},
  mixins: [
    clickaway
  ],

  props: {
    items: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      travellers: []
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'plans',
      'currentPlan'
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

    adultsTotalPrices () {
      let sum = 0
      for (let i = 0; i < this.items.length; i++) {
        sum += this.items[i]['price']['amount'] * this.adultsCount
      }

      return sum
    },

    childrenTotalPrices () {
      let sum = 0
      for (let i = 0; i < this.items.length; i++) {
        sum += this.items[i]['price']['amount'] * this.childrenCount
      }

      return sum
    },

    infantsTotalPrices () {
      let sum = 0
      for (let i = 0; i < this.items.length; i++) {
        sum += this.items[i]['price']['amount'] * this.infantsCount
      }

      return sum
    },

    totalPrice () {
      return this.infantsTotalPrices
        + this.childrenTotalPrices
        + this.adultsTotalPrices
    }
  },

  created () {
    this.travellers = this.currentPlan.travellers
  },

  methods: {
    hide () {
      this.$emit('hide')
    }
  }
}
</script>
