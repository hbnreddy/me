import {cloneDeep} from 'lodash'

export default {
  setLocales (state, payload) {
    state.notifications = cloneDeep(payload)
  },

  setNotifications (state, payload) {
    state.notifications = {
      ...payload
    }
  }
}
