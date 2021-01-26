import { mapGetters } from 'vuex'
import * as moment from 'moment'

export const query = {
  computed: {
    ...mapGetters([
      'query'
    ]),

    currency () {
      return this.query.currency
    },

    queryDays () {
      let days = []
      let a = moment(this.query.start_date)
      let b = moment(this.query.end_date)

      let diff = b.diff(a, 'days') + 1

      while (diff) {
        days.push({
          date: a.format(this.$const('BASE_DATE_FORMAT')),
          day: a.date(),
          month: a.format('MMMM'),
          remainingHours: 24
        })

        a.add(1, 'days')

        diff--
      }

      return days
    },

    queryTravellers () {
      const items = this.query.travellers

      let travellers = []
      Object.keys(items).forEach(key => {
        for (let i = 0; i < items[key]; ++i) {
          travellers.push({
            reference_key: `${key[0].toUpperCase()}${i + 1}`,
            type: key
          })
        }
      })

      return travellers
    }
  }
}
