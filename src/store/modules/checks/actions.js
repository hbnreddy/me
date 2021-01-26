import checkout from '../checkout'
import auth from '../auth'

export default {
  clearErrors ({ commit }) {
    const keys = [
      'travellers',
      'travel',
      'hotels',
      'activities'
    ]

    Object.values(keys).forEach(key => {

      commit('setStep', {
        key,
        value: {
          complete: true,
          errors: []
        }
      })
    })
  },

  verifySteps ({ commit }) {
    const currentPlan = checkout.state.plans[0]

    const steps = {
      travellers: currentPlan.travellers.every(e => e.email) &&
        currentPlan.travellers.findIndex(e => e.email === auth.state.user.email) >= 0,
      travel: true,
      hotels: true,
      activities: true
    }

    Object.keys(steps).forEach(key => {
      const state = {
        key,
        value: {
          complete: currentPlan.currentStep !== 'planning' && steps[key],
          errors: currentPlan.currentStep === 'planning' || steps[key] ? [] : ['This step is not completed.']
        }
      }

      commit('setStep', state)
    })
  }
}
