import axios from 'axios'
import { orderBy } from 'lodash'
import {KEY_DISABLED, KEY_ENABLED} from '../states'
import {SLUG_CITIES, SLUG_CONTINENTS, SLUG_COUNTRIES, SLUG_PLACES, SLUG_THEMES} from '../slugs'

export default {
  async setEntityState ({}, {slug, id, state}) {

    return await axios
      .post(`/api/${slug}/set-state`, {
        id,
        state
      })
      .then(r => {
        return r.data.success
          ? r.data.entity
          : false
      })
  },

  async fetchContinents ({commit}, {state}) {
    commit('setContinents', {
      key: state ? KEY_ENABLED : KEY_DISABLED,
      data: await axios
        .get(`/api/${SLUG_CONTINENTS}`, {
          params: {
            state
          }
        })
        .then(r => {
          return r.data.entities
        })
    })
  },

  async fetchCountries ({commit}, params) {
    commit('setCountries', {
      key: params.state ? KEY_ENABLED : KEY_DISABLED,
      data: await axios
        .get(`/api/${SLUG_COUNTRIES}`, {
          params
        })
        .then(r => {
          return orderBy(r.data.entities, 'name')
        })
    })
  },

  async fetchCities ({commit}, params) {
    commit('setCities', {
      key: params.state ? KEY_ENABLED : KEY_DISABLED,
      data: await axios
        .get(`/api/${SLUG_CITIES}`, {
          params
        })
        .then(r => {
          return orderBy(r.data.entities, 'name')
        })
    })
  },

  async fetchCitiesWeathers ({commit}, params) {
    commit('setCities', {
      key: params.state ? KEY_ENABLED : KEY_DISABLED,
      data: await axios
        .get(`/api/${SLUG_CITIES}`, {
          params
        })
        .then(r => {
          return orderBy(r.data.entities, 'name')
        })
    })
  },

  async fetchPlaces ({commit}, params) {
    commit('setPlaces', await axios
      .get(`/api/${SLUG_PLACES}`, {
        params
      })
      .then(r => {
        return r.data.entities
      })
    )
  },

  async fetchVenues ({commit}, params) {
    commit('setVenues', await axios
      .get(`/api/venues`, {
        params
      })
      .then(r => {
        return r.data.entities
      })
    )
  },

  async venuesTotalCount () {
    return await axios
      .get(`/api/venues/total-count`)
      .then(r => {
        return r.data
      })
  },

  async fetchPlace ({commit}, {id}) {
    commit('setPlace', await axios
      .get(`/api/places/${id}`)
      .then(r => {
        return r.data.entity
      }))
  },

  async fetchThemes ({commit}) {
    commit('setThemes', await axios
      .get(`/api/${SLUG_THEMES}`)
      .then(r => {
        return r.data.entities
      }))
  },

  async fetchMarkers ({commit}, params) {
    commit('setMarkers', await axios
      .get(`/api/markers`, {
        params
      })
      .then(r => {
        return r.data.entities
      }))
  },

  async setMarkerTheme ({}, payload) {
    return await axios
      .post(`/api/markers/set-theme`, payload)
      .then(r => {
        return r.data.success
      })
  },

  async setVenuePlace ({}, data) {
    let url = `/api/venues/set-place`

    return await axios
      .post(url, data)
      .then(r => {
        return r.data
      })
  },

  async storeTheme ({}, {theme}) {
    let url = `/api/themes/store`

    if (theme.id) {
      url += '/' + theme.id
    }

    return await axios
      .post(url, {
        ...theme
      })
      .then(r => {
        return r.data.entity
      })
  },

  async deleteTheme ({}, {id}) {
    return await axios
      .post(`/api/themes/delete`, {
        id
      })
      .then(r => {
        return r.data.success
      })
  }
}
