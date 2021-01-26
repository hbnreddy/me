export const API_URL = 'http://127.0.0.1:8000'

const CONSTANTS = {
    API_URL: 'http://127.0.0.1:8000',

    ADULTS: 'adults',
    CHILDREN: 'children',
    INFANTS: 'infants',

    BASE_DATE_FORMAT: 'YYYY-MM-DD',
    PICKER_DATE_FORMAT: 'ddd, D MMM',

    DATE_TYPE_EXACT: 'exact',
    DATE_TYPE_FLEXIBLE: 'flexible',

    ARRIVAL_TIME_FORMAT: 'hh:mm A',

    TRIP_TYPES: [{
            text: '1 Week in',
            value: '1_week_trip'
        },
        {
            text: '2 Weeks in',
            value: '15_days_trip'
        },
        {
            text: 'Weekend in',
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