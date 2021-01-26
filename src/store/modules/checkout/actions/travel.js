import axios from 'axios'
import {API_URL} from '../../../../bootstrap/constants'

export default {
  async checkoutTravel ({state, dispatch}) {
    const plan = state.plans[0]

    const travellers = plan.travellers
    const items = plan.items.filter(e => e.type === 'travel')

    let responses = []
    for (const item of items) {
      const response = await dispatch('checkoutTravelItem', {
        routingId: item.routing_id,
        travelId: item.id,
        data: {
          travellers,
          card_number: '5454545454545454',
          card_type: 'MasterCard',
          expiry_date: '14/21',
          security_code: '221',
          issue_number: '0'
        }
      })

      responses.push(response)
    }

    return responses
  },

  async checkoutTravelItem ({dispatch}, {routingId, travelId, data}) {
    return await axios
      .post(`${API_URL}/travel/${routingId}/${travelId}/process`, data)
      .then(r => {
        if (r.data.entity) {
          dispatch('updateItem', r.data.entity)

          return true
        } else {
          alert(`Travel Error: ${routingId}.${travelId}`)
        }
      })
      .catch(e => {
        return e.response.data
      })
  },

  async checkoutTravelGroup ({ state }) {
    const plan = state.plans[0]
    const items = plan.items.filter(e => e.type === 'travel')

    if (!items.length) {
      return false
    }

    const group = items.map(e => {
      return {
        id: e.id,
        routing_id: e.routing_id
      }
    })

    return await axios
      .post(`${API_URL}/travel/check-group`, { group })
      .then(r => {
        return r.data
      })
      .catch(e => {
        return false
      })
  }
}
