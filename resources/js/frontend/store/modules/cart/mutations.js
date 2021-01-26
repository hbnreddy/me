import { cloneDeep } from 'lodash'

export default {
  setCart (state, payload) {
    state.cart = cloneDeep(payload)
  },

  setPlans (state, payload) {
    state.plans = cloneDeep(payload)
  },

  setCartItems (state, payload) {
    state.cartItems = cloneDeep(payload)
  },

  setCurrentPlanId (state, payload) {
    state.currentPlanId = payload
  },

  setCurrentItinerary (state, payload) {
    state.currentItinerary = {
      ...payload
    }
  }
}
