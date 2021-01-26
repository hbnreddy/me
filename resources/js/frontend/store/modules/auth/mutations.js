import {cloneDeep} from 'lodash'

export default {
  setAuthUser (state, payload) {
    state.authUser = cloneDeep(payload)
  }
}
