<template>
  <div class="coupon-block">
    <div
      v-if="value.added"
      class="total-discount"
    >
      <div class="text d-flex justify-content-between">
        <div class="mr-3">
          {{ __('Discount Coupon') }}
        </div>

        <div class="coupon">
          <input
            v-model="value.code"
            :class="{ added: value.added }"
            type="text"
            class="coupon-input"
            disabled
          >

          <div
            class="coupon-btn"
            @click.prevent="value.added = false"
          >
            X
          </div>
        </div>
      </div>

      <div>
        {{ plan.currency }} {{ discount.amount }}
      </div>
    </div>

    <div
      v-else
      class="total-discount justify-content-between"
    >
      <div class="text">
        {{ __('Discount Coupon') }}
      </div>

      <div class="coupon">
        <input
          v-model="value.code"
          :class="{ added: value.added }"
          type="text"
          class="coupon-input"
        >

        <div
          class="coupon-btn plus"
          @click.prevent="value.added = true"
          @keypress.enter.prevent="value.added = true"
        >
          <i class="fa fa-plus" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Object,
      required: true
    },

    discount: {
      type: Object,
      required: true
    },

    currency: {
      type: String,
      default: () => '(def) $'
    }
  }
}
</script>
