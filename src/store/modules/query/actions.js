export default {
  async fetchOriginCity ({ state, dispatch }, id) {
    state.originCity = await dispatch('fetchCity', id)
  }
}
