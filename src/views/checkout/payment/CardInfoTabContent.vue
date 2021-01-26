<template>
  <div class="card-info py-4">
    <div class="container">
      <div class="row mb-5">
        <div class="col-md-6">
          <div class="flex-grow-1 h5 text-muted">
            Make your payments securely through your bank account
          </div>
        </div>

        <div class="col-md-6">
          <div class="d-flex justify-content-end">
            <img
              src="/images/payment/card-type.jpg"
              alt="Visa/MasterCard"
              class="payment-card-type-img"
            >
          </div>
        </div>
      </div>

      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <form class="form mb-4">
        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label for="name" class="label">
                Cardholder Name
              </label>

              <input
                id="name"
                v-model="cardholder.name"
                type="text"
                class="form-control"
                required
              >
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="month" class="label">
                Expiry Date
              </label>

              <select
                id="month"
                v-model="cardholder.month"
                class="form-control"
                required
              >
                <option
                  v-for="(month, index) in months"
                  :key="index"
                  :value="index + 1"
                >
                  {{ month }}
                </option>
              </select>
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="year" class="label">&nbsp;</label>

              <select
                id="year"
                v-model="cardholder.year"
                class="form-control"
                required
              >
                <option
                  v-for="(year, index) in years"
                  :key="index"
                >
                  {{ year }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-8">
            <div class="form-group">
              <label for="card-number" class="label">
                Card Number
              </label>

              <input
                id="card-number"
                v-model="cardholder.card_number"
                type="text"
                class="form-control"
                required
              >
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="cvv" class="label">
                CVV
              </label>

              <input
                id="cvv"
                v-model="cardholder.cvv"
                type="number"
                class="form-control"
                required
              >
            </div>
          </div>

          <div class="col-md-2">
            <div class="form-group">
              <label for="issuer-nr" class="label">
                Issuer nr.
              </label>

              <input
                id="issuer-nr"
                v-model="cardholder.issuer"
                type="number"
                class="form-control"
              >
            </div>
          </div>
        </div>
      </form>

      <div class="d-flex justify-content-end pb-4">
        <button
          class="btn btn-danger w-25 m-1"
          @click.prevent="hide($event)"
        >
          <i class="fa fa-close icon" />
          {{ __('Cancel') }}
        </button>

        <button
          class="btn btn-warning w-25 m-1"
          @click.prevent="submit()"
        >
          <i class="fa fa-check icon" />
          {{ __('Pay now') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import moment from 'moment'
import { creditCard } from '../../../mixins/credit-card'

export default {
  mixins: [
    creditCard
  ],

  data () {
    return {
      error: false,
      cardholder: {
        name: '',
        month: '',
        year: '',
        card_number: '5454545454545454',
        cvv: '123',
        issuer: '0'
      }
    }
  },

  computed: {
    months () {
      return moment.monthsShort()
    },

    years () {
      const lastYear = moment().add(10, 'year')

      let currentYear = moment()
      let years = []

      while (currentYear.format('YYYY') <= lastYear.format('YYYY')) {
        years.push(currentYear.format('YYYY'))
        currentYear.add(1, 'year')
      }

      return years
    }
  },

  methods: {
    hide () {
      this.$emit('hide')
    },

    resetCardholder () {
      this.cardholder = {
        name: '',
        month: '',
        year: '',
        card_number: '5454545454545454',
        cvv: '123',
        issuer: '0'
      }
    },

    submit () {
      if (!this.isValid()) {
        return false
      }

      let month = parseInt(this.cardholder.month)
      month = month < 10 ? `0${month}` : month
      const year = this.cardholder.year

      this.$emit('submit', {
        name: this.cardholder.name,
        card_number: this.cardholder.card_number,
        security_code: this.cardholder.cvv,
        expiry_date: `${month}/${year}`,
        issue_number: this.cardholder.issuer,
        card_type: 'MasterCard'
      })

      this.resetCardholder()
    },

    isValid () {
      this.error = false

      const hasEmpty = Object.values(this.cardholder).some(e => !e)
      if (hasEmpty) {
        this.error = 'All fields are required.'

        return false
      }

      if (!this.isValidCardNumber(this.cardholder.card_number)) {
        this.error = 'Invalid card number.'

        return false
      }

      if (!this.isValidCVV(this.cardholder.cvv)) {
        this.error = 'Invalid CVV.'

        return false
      }

      return true
    }
  }
}
</script>
