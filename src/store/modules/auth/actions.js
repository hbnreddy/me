import {API_URL} from '../../../bootstrap/constants'
import axios from 'axios'

export default {
  async register ({ state }, params) {
    return await axios
      .post(`${API_URL}/auth/register`, params)
      .then(r => {
        return r.data.success
      })
      .catch(e => {
        return false
      })
  },

  async login ({ state }, { email, password }) {
    return await axios
      .post(`${API_URL}/auth/login`, {
        email,
        password
      })
      .then(r => {
        return r.data
      })
      .catch(e => {
        return false
      })
  },

  logout ({ dispatch }) {
    dispatch('storeUser', null)
    dispatch('storeToken', null)
  }
}
