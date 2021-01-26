<template>
  <div
    :id="id"
    :aria-labelledby="`plan-${plan.id}-tab`"
    class="tab-pane fade show active"
    role="tabpanel"
  >
    <div class="box box-border">
      <estimate-nav />

      <div
        id="pills-tabContent"
        class="plan-content tab-content"
      >
        <all-sumarry
          :totals="plan.totals"
        />

        <individual-estimate
          :travellers="plan.travellers"
        />

        <div
          v-if="coupon.added"
          class="total-discount"
        >
          <div class="text d-flex">
            <span class="mr-3">{{ __('Coupon discount') }}</span>

            <div class="coupon">
              <input
                v-model="coupon.code"
                :class="{ added: coupon.added }"
                type="text"
                class="coupon-input"
                disabled
              >

              <div
                class="coupon-btn"
                @click.prevent="coupon.added = false"
              >
                X
              </div>
            </div>
          </div>

          <div>
            {{ query.currency.symbol }} {{ discount.amount }}
          </div>
        </div>

        <div
          v-else
          class="total-discount justify-content-start"
        >
          <div class="text mr-4">
            {{ __('Coupon discount') }}
          </div>

          <div class="coupon">
            <input
              v-model="coupon.code"
              :class="{ added: coupon.added }"
              type="text"
              class="coupon-input"
            >

            <div
              class="coupon-btn plus"
              @click.prevent="coupon.added = true"
              @keypress.enter.prevent="coupon.added = true"
            >
              +
            </div>
          </div>
        </div>

        <div class="total-estimate">
          <div>{{ __('Total estimate') }}</div>

          <div style="font-size: 20px;">
            {{ query.currency.symbol }} {{ plan.totals ? plan.totals.total_price : totalPriceTemp }}
          </div>
        </div>

        <div
          v-if="checkoutStep === 1"
          class="checkout-btn-block"
          @click.prevent="$store.commit('setCheckoutStep', 2)"
        >
          <a
            href="#"
            class="btn-checkout"
          >
            {{ __('Update travellers details') }}
          </a>
        </div>

        <div
          v-if="checkoutStep === 2"
          class="checkout-btn-block-fly"
          @click.prevent="goToStep(3)"
        >
          <a
            href="#"
            class="btn-checkout d-flex justify-content-center align-items-center"
          >
            <loading-circle
              :loading="loading"
              :small="true"
              :color="'#ffffff'"
              class="mr-2"
            />

            {{ __('Proceed to Check out') }}
          </a>
        </div>

        <div
          v-if="checkoutStep === 3"
          class="checkout-btn-block-fly"
          @click.prevent="goToStep(2)"
        >
          <a
            href="#"
            class="btn-checkout"
          >
            {{ __('Back to travellers') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import EstimateNav from './EstimateNav'
import LoadingCircle from '../../../../../components/LoadingCircle'
import AllSumarry from './AllSumarry'
import IndividualEstimate from './IndividualEstimate'

import {mapGetters} from 'vuex'
import axios from 'axios'

export default {
  components: {
    LoadingCircle,
    EstimateNav,
    AllSumarry,
    IndividualEstimate
  },

  props: {
    id: {
      type: String,
      required: true
    },

    plan:  {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      loading: false,
      coupon: {
        code: '',
        added: false
      },
      totalPriceTemp: 555,
      discount: {
        amount: 450
      }
    }
  },

  computed: {
    ...mapGetters([
      'query',
      'checkoutStep'
    ])
  },

  methods: {
    async goToStep (number) {
      this.loading = true

      if (number === 3) {
        await axios
          .post(`/api/cart/checkout`)
          .then(r => {
            if (r.data.success) {
              this.$store.commit('setCheckoutStep', number)
            } else if (r.data.message) {
              if (r.data.message.message) {
                this.$notification.show(r.data.message.message)
              } else {
                this.$notification.show(r.data.message)
              }
            }
          })
      } else {
        this.$store.commit('setCheckoutStep', number)
      }

      this.loading = false
    }
  }
}
</script>
