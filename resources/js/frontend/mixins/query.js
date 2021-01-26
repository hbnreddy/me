import { mapGetters } from 'vuex'
import * as moment from 'moment'

export const query = {
  computed: {
    ...mapGetters([
      'query'
    ]),

    currency () {
      return this.query.currency.symbol
    },

    queryDate () {
      return this.query.date
    },

    queryDays () {
      let days = []
      const date = this.query.date

      let a = moment(date.start_date)
      let b = moment(date.end_date)

      if (date.date_type !== 'exact') {
        b = b.endOf('month')
      }

      let diff = b.diff(a, 'days') + 1

      while (diff) {
        days.push({
          date: a.format(this.$const('BASE_DATE_FORMAT')),
          day: a.date(),
          month: a.format('MMMM'),
          // remainingHours: await this.dayAvailability(a.format(this.$const('BASE_DATE_FORMAT')))
          remainingHours: 18
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
