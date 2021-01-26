const CONSTANTS = {
  ADULTS: 'adults',
  CHILDREN: 'children',
  INFANTS: 'infants',

  BASE_DATE_FORMAT: 'YYYY-MM-DD',
  PICKER_DATE_FORMAT: 'ddd, D MMM',

  DATE_TYPE_EXACT: 'exact',
  DATE_TYPE_FLEXIBLE: 'flexible',

  ARRIVAL_TIME_FORMAT: 'hh:mm A',

  TRIP_TYPES: [
    {
      text: '1 Week Trip',
      value: '1_week_trip'
    },
    {
      text: '15 Days Trip',
      value: '15_days_trip'
    },
    {
      text: 'Weekend Trip',
      value: 'weekend_trip'
    }
  ],

  ROUTE_TYPE_ONEWAY: 'oneway',
  ROUTE_TYPE_ROUND: 'round'
}

const ConstInstaller = {
  install: (Vue) => {
    Vue.prototype.$const = (key) => {
      return CONSTANTS[key]
    }
  }
}

export default ConstInstaller
