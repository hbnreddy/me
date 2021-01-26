import axios from 'axios'
import {API_URL} from '../../../bootstrap/constants'

export default {
  async fetchHotels ({}, params) {
    return await axios
      .post(`${API_URL}/hotels`, params)
      .then(r => {
        return r.data
      })
  },

  async fetchHotelOffers ({}, { id, searchKey }) {
    return await axios
      .get(`${API_URL}/hotels/${id}`, {
        params: {
          search_key: searchKey
        }
      })
      .then(r => {
        return r.data.entity.offers
      })
  }
}
