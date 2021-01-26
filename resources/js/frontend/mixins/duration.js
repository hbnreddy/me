export const duration = {
  data () {
    return {
      baseTimeslots: [
        '06:00',
        '07:00',
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
        '21:00',
        '22:00',
        '23:00'
      ]
    }
  },

  methods: {
    processedDurationRange (durationRange) {
      if (!durationRange) {
        return '12h'
      }

      const duration = this.durationInMinutes(durationRange)

      let hours = parseInt(duration / 60)

      let minutes = duration - (60 * hours)

      if (minutes) {
        return `${hours}h ${minutes}m`
      }

      return `${hours}h`
    },

    durationInMinutes (durationRange) {
      if (!durationRange) {
        return 12 * 60
      }

      let value = ''

      const range = durationRange

      value = range.min ? range.min.substr(2, range.min.length - 2) : ''
      value = range.min && range.max ? value + ' - ' : value

      let hour = ''
      if (range.max) {
        hour = parseInt(range.max.substr(2, 2))
        value += range.max.substr(2, range.max.length - 2)
      }

      value = value.toLowerCase()

      if (parseInt(hour) === 24 || value === 'd') {
        return 12 * 60
      }

      let totalMinutes = 0

      if (value.includes('h')) {
        const hours = parseInt(value.split('h')[0])
        totalMinutes += hours * 60
      }

      if (value.includes('m')) {
        let right = value

        if (value.includes('h')) {
          right = value.split('h')[1]
        }

        const mins = parseInt(right.split('m')[0])
        totalMinutes += mins
      }

      return totalMinutes
    },

    timeAfterMinutes (time, minutes) {
      const splitTime = time.split(':')

      const startTimeHour = parseInt(splitTime[0])
      const startTimeMinute = parseInt(splitTime[1])

      const plusHours = parseInt(minutes / 60)
      const plusMinutes = minutes % 60

      let timeHour = startTimeHour + plusHours
      let timeMinute = startTimeMinute + plusMinutes

      timeHour += parseInt(timeMinute / 60)
      timeMinute = timeMinute % 60

      if (timeHour > 23) {
        timeHour = timeHour % 24
      }

      if (timeHour < 10) {
        timeHour = `0${timeHour}`
      }

      if (timeMinute < 10) {
        timeMinute = `0${timeMinute}`
      }

      return `${timeHour}:${timeMinute}`
    }
  }
}
