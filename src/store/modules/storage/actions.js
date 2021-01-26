export default {
  storeToken ({}, token) {
    localStorage.setItem('token', token)
  },

  checkAuth ({ commit }) {
    let user = localStorage.getItem('user')

    if (user) {
      commit('setUser', JSON.parse(user))
    }
  },

  storeUser ({ commit }, entity) {
    localStorage.setItem('user', JSON.stringify(entity))

    commit('setUser', entity)
  },

  initCart ({ commit }) {
    let cart = localStorage.getItem('cart')

    if (cart) {
      cart = JSON.parse(cart)

      commit('setPlans', cart.plans)
    }
  },

  async resetCart ({ commit }) {
    localStorage.removeItem('cart')

    await commit('setPlans', [])
  },

  storePlan ({ commit }, entity) {
    let cart = localStorage.getItem('cart')

    if (!cart) {
      cart = {
        plans: []
      }
    } else {
      cart = JSON.parse(cart)
    }

    const index = cart.plans.findIndex(e => {
      return e.id === entity.id
    })

    if (index >= 0) {
      cart.plans.splice(index, 1, entity)
    } else {
      cart.plans.push(entity)
    }

    commit('setPlans', cart.plans)

    localStorage.setItem('cart', JSON.stringify(cart))
  },

  async removeTravelItems ({ commit }) {
    let cart = JSON.parse(localStorage.getItem('cart'))
    let currentPlan = cart.plans[0]

    currentPlan.items = currentPlan.items
      .filter(e => e.type !== 'travel')

    localStorage.setItem('cart', JSON.stringify({
      plans: [currentPlan]
    }))

    commit('setPlans', [currentPlan])

    return true
  },

  async storeItem ({ commit }, item) {
    let cart = JSON.parse(localStorage.getItem('cart'))
    let currentPlan = cart.plans[0]

    if (item.type === 'travel') {
      currentPlan.items = currentPlan.items.filter(e => {
        return e.type !== 'travel' || item.group_id === e.group_id
      })
    }

    let found = false
    if (currentPlan.items.length) {
      let index = -1

      const isTravelType = item.type === 'travel'
      const isHotelType = item.type === 'hotel'

      if (isTravelType) {
        index = currentPlan.items.findIndex(e => {
          return e.id === item.id && e.group_id === item.group_id
        })
      } else if (isHotelType) {
        index = currentPlan.items.findIndex(e => {
          return e.type === item.type
        })
      } else {
        index = currentPlan.items.findIndex(e => {
          return e.id === item.id
        })
      }

      if (index >= 0) {
        found = true

        currentPlan.items.splice(index, 1, item)
      }
    }

    if (!found) {
      currentPlan.items.push(item)
    }

    localStorage.setItem('cart', JSON.stringify({
      plans: [currentPlan]
    }))

    commit('setPlans', [currentPlan])

    return true
  },

  async refreshItem ({ commit }, item) {
    let cart = JSON.parse(localStorage.getItem('cart'))

    let index = cart.plans[0].items.findIndex(e => e.id === item.id)

    if (index < 0) {
      return false
    }

    cart.plans[0].items.splice(index, 1, {
      ...cart.plans[0].items[index],
      ...item
    })

    localStorage.setItem('cart', JSON.stringify(cart))

    commit('setPlans', cart.plans)

    return true
  },

  async removeItem ({ commit }, id) {
    let cart = JSON.parse(localStorage.getItem('cart'))

    /**
     * TODO: Setup for each plan.
     */
    let found = false
    if (cart.plans[0].items) {
      const index = cart.plans[0].items.findIndex(e => {
        return e.id.toString() === id.toString()
      })

      if (index >= 0) {
        found = true
        cart.plans[0].items.splice(index, 1)
      }
    }

    if (!found) {
      return false
    }

    localStorage.setItem('cart', JSON.stringify(cart))

    commit('setPlans', cart.plans)

    return true
  },

  storeTraveller ({ commit }, traveller) {
    let cart = JSON.parse(localStorage.getItem('cart'))

    /**
     * TODO: Setup for each plan.
     */

    const index = cart.plans[0].travellers.findIndex(e => {
      return e.id === traveller.id
    })

    cart.plans[0].travellers.splice(index, 1, traveller)

    localStorage.setItem('cart', JSON.stringify(cart))

    commit('setPlans', cart.plans)
  }
}
