import {API_URL} from '../../../bootstrap/constants'
import axios from 'axios'

export default {
  async fetchFlightGroups ({}, params) {
    return await axios
      .post(`${API_URL}/travel/groups`, params)
      .then(r => {
        return r.data.entities
      })
  },

  async fetchFlightTerms ({}, { routingId, itemId }) {
    return await axios
      .get(`${API_URL}/travel/${routingId}/${itemId}/terms`)
      .then(r => {
        return r.data.entities
      })
  }
}
