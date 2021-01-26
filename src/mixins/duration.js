export const duration = {
  data () {
    return {
      baseTimeslots: [
        '06:00',
        '06:30',
        '07:00',
        '07:30',
        '08:00',
        '08:30',
        '09:00',
        '09:30',
        '10:00',
        '10:30',
        '11:00',
        '11:30',
        '12:00',
        '12:30',
        '13:00',
        '13:30',
        '14:00',
        '14:30',
        '15:00',
        '15:30',
        '16:00',
        '16:30',
        '17:00',
        '17:30',
        '18:00',
        '18:30',
        '19:00',
        '19:30',
        '20:00',
        '20:30',
        '21:00',
        '21:30',
        '22:00',
        '22:30',
        '23:00',
        '23:30'
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
