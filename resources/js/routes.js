import ExplorePage from './frontend/pages/explore/ExplorePage'
import CityPage from './frontend/pages/explore/components/CityPage'
import PlacePage from './frontend/pages/place/PlacePage'

export const routes = [
  {
    name: 'explore',
    path: '/explore',
    component: ExplorePage
  },
  {
    name: 'city',
    path: '/city/:id',
    component: CityPage
  },
  {
    name: 'place',
    path: '/explore/place/:id',
    component: PlacePage
  }
]
