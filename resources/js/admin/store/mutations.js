import {cloneDeep} from 'lodash'

export default {
  setContinents (state, payload) {
    if (payload.key) {
      state.continents[payload.key] = cloneDeep(payload.data)

      return true
    }

    state.continents = cloneDeep(payload)
  },

  setCountries (state, payload) {
    if (payload.key) {
      state.countries[payload.key] = cloneDeep(payload.data)

      return true
    }

    state.countries = cloneDeep(payload)
  },

  setCities (state, payload) {
    if (payload.key) {
      state.cities[payload.key] = cloneDeep(payload.data)

      return true
    }

    state.cities = cloneDeep(payload)
  },

  setPlaces (state, payload) {
    state.places = cloneDeep(payload)
  },

  setPlace (state, payload) {
    state.place = cloneDeep(payload)
  },

  setVenues (state, payload) {
    state.venues = cloneDeep(payload)
  },

  setLoading (state, payload) {
    state.loading = cloneDeep(payload)
  },

  setThemes (state, payload) {
    state.themes = cloneDeep(payload)
  },

  setMarkers (state, payload) {
    state.markers = cloneDeep(payload)
  },

  setUser (state, payload) {
    state.user = cloneDeep(payload)
  }
}
