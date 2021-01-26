export const creditCard = {
  methods: {
    isValidCardNumber (number) {
      return this.isValidVisa(number)
        || this.isValidMastercard(number)
    },

    isValidVisa (number) {
      return (/^(?:4[0-9]{12}(?:[0-9]{3})?)$/).test(number)
    },

    isValidMastercard (number) {
      return (/^(?:5[1-5][0-9]{14})$/).test(number)
    },

    isValidCVV (number) {
      return number.length === 3
    }
  }
}
