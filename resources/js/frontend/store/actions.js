import axios from 'axios'

export default {
  async processQuery ({state, commit}) {
    return await axios
      .get(`/explore`, {
        params: {
          themes_ids: Object.keys(state.query.query.themes)
        }
      })
      .then(r => {
        if (r.data.success) {
          commit('setCities', r.data.entities)
        }

        return r.data.success
      })
  },

  notify ({state, commit}) {
    commit('setNotifications', state.notifications + 1)
  }
}
