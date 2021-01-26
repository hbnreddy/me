import axios from 'axios'
import {API_URL} from '../../../bootstrap/constants'
import auth from '../auth'
import checkout from '../checkout'

export default {
  async fetchOrders ({ state }) {
    await axios
      .get(`${API_URL}/orders`)
      .then(r => {
        state.orders = r.data.entities
      })
  },

  async fetchOrder ({}, id) {
    return await axios
      .get(`${API_URL}/orders/${id}`)
      .then(r => {
        return r.data.entity
      })
  },

  async makeOrder () {
    const plan = checkout.state.plans[0]

    return await axios
      .post(`${API_URL}/orders/create`, {
        user_id: auth.state.user.id,
        name: plan.name,
        start_date: plan.start_date,
        end_date: plan.end_date,
        origin_city_id: plan.origin_city,
        travellers: plan.travellers,
        items: plan.items
      })
      .then(r => {
        return r.data
      })
      .catch(e => {
        return e.response.data
      })
  },

  async getBookingStatus ({}, { id, type }) {
    await axios
      .get(`${API_URL}/${type}/${id}/booking-status`)
      .then(r => {
      })
  }
}
