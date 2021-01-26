import axios from 'axios'
import {API_URL} from '../../../../bootstrap/constants'

export default {
  async checkoutHotel ({ state }, search_key) {
    const plan = state.plans[0]
    const item = plan.items.find(e => e.type === 'hotel')

    if (!item) {
      return false
    }

    return await axios
      .post(`${API_URL}/hotels/check-group`, {
        hotel_id: item.id,
        offers: item.offers.map(o => o.offer_id),
        search_key
      })
      .then(r => {
        return r.data
      })
      .catch(e => {
        return false
      })
  }
}
