export default {
  setCheckoutErrors (state, payload) {
    state.checkoutErrors = {
      ...payload
    }
  }
}
