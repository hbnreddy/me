import {cloneDeep} from 'lodash'

export default {
  setLoading (state, payload) {
    state.loading = cloneDeep(payload)
  },

  setNotifications (state, payload) {
    state.notifications = {
      ...payload
    }
  },

  setCity (state, payload) {
    state.city = cloneDeep(payload)
  },

  setPlace (state, payload) {
    state.place = cloneDeep(payload)
  },

  setThemes (state, payload) {
    state.themes = cloneDeep(payload)
  }
}
