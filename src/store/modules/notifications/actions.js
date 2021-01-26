import axios from 'axios'
import {API_URL} from '../../../bootstrap/constants'

export default {
  async fetchNotifications ({ state }) {
    return await axios
      .get(`${API_URL}/notifications`)
      .then(r => {
        state.notifications = r.data.entities
      })
  },

  notify ({state, commit}) {
    commit('setNotifications', state.notifications + 1)
  }
}
