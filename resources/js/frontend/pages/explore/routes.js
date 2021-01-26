import CitiesList from './components/CitiesList'
import CityView from './components/CityPage'

export const routes = [
  {
    name: 'explore',
    path: '/',
    component: CitiesList
  },
  {
    name: 'city',
    path: '/city/:id',
    component: CityView
  }
]
