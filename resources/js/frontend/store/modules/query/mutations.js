import {cloneDeep} from 'lodash'

export default {
  setQuery (state, payload) {
    state.query = cloneDeep(payload)
  },

  setCities (state, payload) {
    payload.sort((a, b) => {
      if (a.added) {
        return -1
      } else if (b.added) {
        return 1
      }

      return 0
    })

    state.cities = cloneDeep(payload)
  }
}
