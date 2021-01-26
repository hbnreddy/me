import ContinentsList from './pages/ContinentsList'
import CountriesList from './pages/CountriesList'
import CitiesList from './pages/CitiesList'
import PlaceView from './pages/PlaceView'
import PlacesOfInterestList from './pages/PlacesList'
import ThemesList from './pages/ThemesList'
import MarkersList from './pages/MarkersList'
import VenuesList from './pages/VenuesList'
import Settings from './pages/Settings'
import Weather from './pages/Weather'
import Dashboard from './pages/Dashboard'
import MediaManagement from './pages/MediaManagement'
import AccessManagement from './pages/AccessManagement'
import LanguageManagement from './pages/LanguageManagement'
import OrdersManagement from './pages/OrdersManagement'
import Order from './pages/Order'

export const routes = [
  {
    name: 'dashboard',
    path: '/',
    component: Dashboard
  },
  {
    name: 'continents',
    path: '/continents',
    component: ContinentsList
  },
  {
    name: 'countries',
    path: '/countries',
    component: CountriesList
  },
  {
    name: 'cities',
    path: '/cities',
    component: CitiesList
  },
  {
    name: 'places-of-interest',
    path: '/places-of-interest',
    component: PlacesOfInterestList
  },
  {
    name: 'themes',
    path: '/themes',
    component: ThemesList
  },
  {
    name: 'markers',
    path: '/markers',
    component: MarkersList
  },
  {
    name: 'venues',
    path: '/venues',
    component: VenuesList
  },
  {
    name: 'place-view',
    path: '/place/:id',
    component: PlaceView
  },
  {
    name: 'media-management',
    path: '/media-management',
    component: MediaManagement
  },
  {
    name: 'weather',
    path: '/weather',
    component: Weather
  },
  {
    name: 'language-management',
    path: '/language-management',
    component: LanguageManagement
  },
  {
    name: 'access-management',
    path: '/access-management',
    component: AccessManagement
  },
  {
    name: 'settings',
    path: '/settings',
    component: Settings
  },
  {
    name: 'orders',
    path: '/orders',
    component: OrdersManagement
  },
  {
    name: 'order',
    path: '/order/:id',
    component: Order
  }
]
