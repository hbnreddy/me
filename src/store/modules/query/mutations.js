import { cloneDeep } from 'lodash'

export default {
    setQuery(state, payload) {
        state.query = cloneDeep(payload)
    },

    processQuery(state, payload) {
        const travellers = payload.travellers.split('-')

        console.log(travellers, "store");

        state.query = {
            ...payload,
            origin_city_id: parseInt(payload.origin_city_id),
            themes: payload.themes
                .split(',')
                .map(e => parseInt(e)),
            travellers: {
                adults: parseInt(travellers[0]),
                children: parseInt(travellers[1]),
                infants: parseInt(travellers[2])
            }
        }
    },

    setCities(state, payload) {
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