import axios from 'axios'

export default {
  async fetchCurrentItinerary ({ state, commit }) {
    return await axios
      .get(`/api/cart/plans/${state.currentPlanId}/itinerary`)
      .then(async r => {
        if (r.data.success) {
          await commit('setCurrentItinerary', r.data.entity)

          return true
        }

        return false
      })
  },

  async storeItemToPlan ({ state, dispatch }, payload) {
    let url = `/api/cart/plans/${state.currentPlanId}/items/store`

    if (payload.update) {
      url = `${url}/${payload.id}`
    }

    return await axios
      .post(url, payload)
      .then(r => {
        if (r.data.success) {
          dispatch('notify')
        }

        return r.data.success
      })
  },

  async removeItemFromPlan ({dispatch}, {plan_id, id}) {
    return await axios
      .post(`/api/cart/plans/${plan_id}/items/${id}/remove`)
      .then(r => {
        if (r.data.success) {
          dispatch('notify')
        }

        return r.data.success
      })
  }
}
