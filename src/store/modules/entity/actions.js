import axios from 'axios'
import { API_URL } from '../../../bootstrap/constants'
import { ACTIVITY_PLACE } from '../../../views/place/place-types'

export default {
    async fetchLocales({ state }) {
        return await axios
            .get(`${API_URL}/languages`)
            .then(r => {
                state.locales = r.data.entities
            })
    },

    async fetchCurrencies({ state }) {
        return await axios
            .get(`${API_URL}/currencies`)
            .then(r => {
                state.currencies = r.data.entities
            })
    },

    async fetchThemes({ commit }) {
        commit('setThemes', await axios
            .get(`${API_URL}/themes`)
            .then(r => {
                return r.data.entities
            }))
    },

    async fetchCity({}, id) {
        return await axios
            .get(`${API_URL}/cities/${id}`)
            .then(r => {
                return r.data.entity
            })
    },

    async fetchCityCounts({}, { id, themes }) {
        return await axios
            .get(`${API_URL}/cities/${id}/counts`, {
                params: {
                    themes
                }
            })
            .then(r => {
                return r.data
            })
    },

    async fetchCheapestExperience({}, cityId) {
        return await axios
            .get(`${API_URL}/cities/${cityId}/cheapest-experience`)
            .then(r => {
                return r.data.value
            })
    },

    async fetchCheapestRoute({}, params) {
        console.log(params);
        return await axios
            .get(`${API_URL}/travel/cheapest`, {
                params
            })
            .then(r => {
                if (!r.data.error) {
                    return r.data.entity
                }
            })
    },

    async fetchCheapestHotel({}, params) {
        return await axios
            .get(`${API_URL}/hotels/cheapest-price`, {
                params
            })
            .then(r => {
                return r.data.value
            })
    },

    async fetchCities({ state }, params) {
        const store = params.store !== false

        return await axios
            .get(`${API_URL}/cities/explore`, {
                params: {
                    offset: params.offset,
                    limit: params.limit,
                    themes: params.themes
                }
            })
            .then(r => {
                if (!store) {
                    return r.data.entities
                }

                const ids = state.cities.map(c => c.id)

                state.cities = [
                    ...state.cities,
                    ...r.data.entities.filter(e => {
                        return !ids.includes(e.id)
                    })
                ]

                return state.cities
            })
    },

    async fetchPlaces({}, params) {
        return await axios
            .get(`${API_URL}/cities/${params.cityId}/places`, {
                params: {
                    limit: params.limit,
                    offset: params.offset,
                    theme_id: params.theme_id
                }
            })
            .then(r => {
                return r.data.entities
            })
    },

    async fetchBestActivityPrice({}, venueId) {
        return await axios
            .get(`${API_URL}/venues/${venueId}/best-activity-price`)
            .then(r => {
                return r.data.entity
            })
    },

    async fetchVenueInformation({}, { venueId, startDate, endDate }) {
        return axios
            .get(`${API_URL}/venues/${venueId}/information`, {
                params: {
                    start_date: startDate,
                    end_date: endDate
                }
            })
            .then(r => {
                return r.data.entity
            })
    },

    async fetchPlace({}, id) {
        return axios
            .get(`${API_URL}/places/${id}`)
            .then(r => {
                return r.data.entity
            })
    },

    async fetchActivitiesDetails({}, { venueId, startDate, endDate }) {
        return await axios
            .get(`${API_URL}/venues/${venueId}/activities-details`, {
                params: {
                    start_date: this.startDate,
                    end_date: this.endDate
                }
            })
            .then(r => {
                return r.data
            })
    },

    async fetchActivities({}, params) {
        return await axios
            .get(`${API_URL}/venues/${params.venueId}/activities`, {
                params
            })
            .then(r => {
                if (r.data.success) {
                    const result = r.data.entities[0]

                    this.entity = {
                        id: result.uuid,
                        price: {
                            value: result.retail_price.value,
                            currency: this.data.query.currency
                        },
                        type: ACTIVITY_PLACE,
                        name: result.title,
                        name_suffix: place.name_suffix,
                        description: result.about,
                        rating: result.reviews_avg,
                        reviews_number: result.reviews_number,
                        activity_uuid: result.uuid,
                        place_id: place.id,
                        city_id: place.city_id,
                        city_code: place.city.code,
                        marker_id: place.marker_id,
                        venue_id: place.venue.id
                    }

                    this.activities.push(result)
                }
            })
    },

    async fetchItemDuration({}, { itemId }) {
        return await axios
            .get(`${API_URL}/activities/${itemId}/duration`)
            .then(r => {
                return r.data.entity
            })
    },

    async fetchActivityDateInfo({}, { itemId, date }) {
        return await axios
            .get(`${API_URL}/activities/${itemId}/date-info`, {
                params: {
                    date
                },
                timeout: 5000
            })
            .then(r => {
                if (r.data.success) {
                    return r.data.entity
                }
            })
    },

    async checkDayAvailability({}, { itemId, date }) {
        return await axios
            .get(`${API_URL}/activities/${itemId}/available/${date}`)
            .then(r => {
                return r.data.entity
            })
    }
}