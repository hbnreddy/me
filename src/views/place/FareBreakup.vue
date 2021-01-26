<template>
  <div class="breakup">
    <div class="title text-left">
      Fare {{ __('breakup') }}
    </div>

    <div class="travellers-wrap text-left">
      <div
        v-for="(row, index) in rows"
        :key="index"
        class="traveller-line"
      >
        <div>
          <div>{{ row.type }}</div>
          <small>{{ row.available }} {{ __('available') }}</small>
        </div>

        <div>{{ row.count }}</div>

        <div class="flex-fill text-right">
          {{ currency.symbol }}
          {{ Math.round(row.count * row.price) }}

          <div>
            <small v-if="row.min_buy > 1">
              {{ __('Min') }}. {{ row.min_buy }}
            </small>

            <small v-if="row.min_buy > 1 && row.max_buy"> | </small>

            <small v-if="row.max_buy">{{ __('Max') }}. {{ row.max_buy }}</small>
          </div>
        </div>
      </div>
    </div>

    <div class="total">
      <div>{{ __('Total') }}</div>

      <div>
        {{ currency.symbol }}
        {{ Math.round(totalPrice) }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    timeslot: {
      type: Object,
      required: true
    },

    travellers: {
      type: Array,
      required: true
    },

    currency: {
      type: Object,
      required: true
    }
  },

  computed: {
    rows () {
      const keys = Object.keys(this.timeslot)
        .filter(key => {
          return [
            'adults',
            'children',
            'infants'
          ].includes(key)
        })

      let rows = []
      for (let i = 0; i < keys.length; ++i) {
        rows.push({
          type: keys[i],
          price: this.timeslot[keys[i]].price,
          min_buy: this.timeslot[keys[i]].min_buy,
          max_buy: this.timeslot[keys[i]].max_buy,
          available: this.timeslot[keys[i]].availability,
          count: this.travellers.filter(e => {
            return e.active && e.type === keys[i]
          }).length
        })
      }

      return rows
    },

    totalPrice () {
      let sum = 0
      this.rows.forEach(e => {
        sum += e.count * e.price
      })

      return sum
    }
  }
}
</script>
