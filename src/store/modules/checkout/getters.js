import queryState from '../query'
import {groupBy, sortBy} from 'lodash'
import moment from 'moment'

export default {
  steps: state => state.steps,
  currentStep: state => state.currentStep,
  plans: state => state.plans,
  currentPlan: (state) => state.plans[0],
  currentPlanId: state => {
    return state.plans.length
      ? state.plans[0].id
      : 'new-plan'
  },
  currentTravellers: state => state.plans[0].travellers,
  itinerary: (state) => {
    const currentPlan = state.plans[0]

    if (!currentPlan) {
      return []
    }

    const originCity = queryState.state.originCity

    const items = currentPlan.items
      .filter(e => {
        return ['standard', 'activity'].includes(e.type)
      })

    const sortedItems = sortBy(items, 'timeslot.start_date')
    let groupedItems = groupBy(sortedItems, 'timeslot.start_date')

    groupedItems = Object.values(groupedItems)
      .flat()
      .map(item => {
        return {
          city_id: item.city_id,
          city_code: item.city_code,
          date: moment(item.timeslot.start_date).format('YYYY-MM-DD')
        }
      })

    groupedItems.unshift({
      city_id: originCity.id,
      city_code: originCity.code,
      date: moment(currentPlan.start_date).format('YYYY-MM-DD')
    })

    groupedItems.push({
      city_id: originCity.id,
      city_code: originCity.code,
      date: moment(currentPlan.end_date).format('YYYY-MM-DD')
    })

    return groupedItems
  }
}
