import axios from 'axios'
import {API_URL} from '../../../../bootstrap/constants'
import checkout from '../../checkout'

import activity from './activity'
import travel from './travel'
import hotel from './hotel'

const actions = {
  async checkoutPayment ({ state }, cardholder) {
    const plan = state.plans[0]

    const standardItems = plan.items
      .filter(e => e.type === 'standard')
    const travelItems = plan.items
      .filter(e => e.type === 'travel')
      .map(e => {
        return {
          id: e.id,
          routing_id: e.routing_id,
          type: e.type
        }
      })
    const hotels = plan.items
      .filter(e => e.type === 'hotel')
      .map(e => {
        return {
          id: e.id,
          routing_id: e.routing_id,
          type: e.type
        }
      })

    const data = {
      items: [
        ...standardItems,
        ...travelItems,
        ...hotels
      ],
      travellers: plan.travellers.map(e => {
        return {
          ...e,
          building_name: 'Building',
          street: 'Street',
          country_code: 'FR',
          extension: '123',
          international_code: '40',
          locality: 'Locality',
          province: 'Province'
        }
      }),
      cardholder
    }

    return await axios
      .post(`${API_URL}/cart/checkout-payment`, data)
      .then(r => {
        return r.data
      })
      .catch(e => {
        return e.response.data
      })
  },

  async setCurrentStep ({ commit, dispatch }, payload) {
    const plan = checkout.state.plans[0]

    if (!plan) {
      return false
    }

    await dispatch('storePlan', {
      ...plan,
      currentStep: payload
    })

    commit('setCurrentStep', payload)
  },

  async createPlan ({}, params) {
    return await axios
      .post(`${API_URL}/cart/plans/create`, params)
      .then(r => {
        return r.data.entity
      })
  },

  async createItem ({ dispatch }, payload) {
    let url = `${API_URL}/cart/items/create`

    return await axios
      .post(url, payload)
      .then(r => {
        if (r.data.success) {
          dispatch('notify')
        }

        return r.data.success
      })
  },

  checkout ({ state, dispatch }) {
    const plan = state.plans[0]

    let steps = plan.steps
    Object.keys(steps).forEach(key => {
      if (!steps[key].complete) {
        this.$eventHub.emit(`${key}.loading`, true)
      }
    })

    const traveller = plan.travellers[0]

    const travelItems = plan.items.filter(e => e.type === 'travel')
    const hotelItems = plan.items.filter(e => e.type === 'hotel')

    travelItems.forEach(item => {
      dispatch('checkoutTravel', {
        routingId: item.routing_id,
        travelId: item.id,
        data: {
          ...traveller,
          building_name: 'Building',
          street: 'Street',
          country_code: 'FR',
          extension: '123',
          international_code: '40',
          locality: 'Locality',
          province: 'Province',
          card_number: '5411666677775555',
          card_type: 'MasterCard',
          expiry_date: '14/21',
          security_code: '221',
          issue_number: '0'
        }
      })
    })

    Object.keys(steps).forEach(key => {
      if (!steps[key].complete) {
        this.$eventHub.emit(`${key}.loading`, false)
      }
    })
  }
}

export default {
  ...actions,
  ...activity,
  ...travel,
  ...hotel
}
