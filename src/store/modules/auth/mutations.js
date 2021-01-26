import {cloneDeep} from 'lodash'

export default {
  setUser (state, payload) {
    state.user = cloneDeep(payload)
  }
}
