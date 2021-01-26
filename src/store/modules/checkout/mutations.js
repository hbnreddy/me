import {cloneDeep} from 'lodash'

export default {
  setCurrentStep (state, payload) {
    state.currentStep = payload
  },

  setStep (state, { key, value }) {
    state.steps = {
      ...state.steps,
      [key]: {
        ...value
      }
    }
  },

  setPlans (state, payload) {
    state.plans = cloneDeep(payload)
  },

  setTravellers (state, payload) {
    state.travellers = payload
  }
}
