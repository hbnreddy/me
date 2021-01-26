import {cloneDeep} from 'lodash'

export default {
  setOrders (state, payload) {
    state.orders = cloneDeep(payload)
  }
}
