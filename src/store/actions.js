import axios from 'axios'
import {API_URL} from '../bootstrap/constants'

export default {
  async processQuery ({state, commit}) {
    return await axios
      .get(`${API_URL}/explore`, {
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
  }
}
