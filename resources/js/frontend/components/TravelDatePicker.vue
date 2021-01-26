<template>
  <div
    class="extended-date-picker-block"
    style="position: absolute;"
  >
    <div>
      <div class="calendar-header">
        <label
          class="radio-container mr-4"
          @click="setType($const('DATE_TYPE_EXACT'))"
        >
          {{ __('Exact dates') }}

          <input
            :checked="picker.type === $const('DATE_TYPE_EXACT')"
            type="radio"
            name="radio"
          >
          <span class="checkmark" />
        </label>

        <label
          v-if="multipleValues"
          class="radio-container"
          @click="setType($const('DATE_TYPE_FLEXIBLE'))"
        >
          {{ __('Flexible dates') }}
          <input
            :checked="picker.type === $const('DATE_TYPE_FLEXIBLE')"
            type="radio"
            name="radio"
          >
          <span class="checkmark" />
        </label>
      </div>

      <div class="date-picker-content">
        <div
          v-if="picker.type === $const('DATE_TYPE_EXACT')"
          id="date-picker"
        />

        <div
          v-else-if="multipleValues"
          class="month-picker"
        >
          <div class="months-view">
            <div class="months-heading">
              <div
                v-for="(trip, index) in $const('TRIP_TYPES')"
                :key="index"
                :class="{ 'active': picker.flexible_type.value === trip.value }"
                class="month-item"
                @click.prevent="onClickTrip(index)"
              >
                {{ trip.text }}
              </div>
            </div>

            <div class="months-content">
              <div
                v-for="(month, index) in months"
                :key="index"
                :class="{ 'active': picker.month === indexOfMonth(month), 'disabled': unavailableMonth(index) === true }"
                class="month-item"
                @click.prevent="onClickMonth(index)"
              >
                {{ month }}
                <span v-if="index === 0 || index === 11">{{ picker.selectedYear }}</span>
              </div>
            </div>
          </div>

          <div class="year-selector">
            <i
              class="fa fa-arrow-up blue-hover"
              @click.prevent="changeYear(picker.selectedYear - 1)"
            />

            <div class="year">
              {{ picker.selectedYear }}
            </div>

            <i
              class="fa fa-arrow-down blue-hover"
              @click.prevent="changeYear(picker.selectedYear + 1)"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="buttons text-right p-2">
      <a
        href="#"
        class="mx-2 text-dark"
        @click.prevent="onClickCancel()"
      >{{ __('Cancel') }}</a>
      <a
        href="#"
        class="mx-2 text-primary"
        @click.prevent="onClickApply()"
      >{{ __('Apply') }}</a>
    </div>
  </div>
</template>

<script>
import * as moment from 'moment'
import {mixin as clickaway} from 'vue-clickaway'
import flatpickr from 'flatpickr'

export default {
  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Object | Boolean,
      required: true
    },

    multipleValues: {
      type: Boolean,
      default: true
    }
  },

  data () {
    return {
      picker: {
        type: this.$const('DATE_TYPE_EXACT'),
        flexible_type: 0,
        month: 0,
        date_string: '',
        start: 0,
        end: 0,
        calendar_view: 0,
        selectedYear: moment().year()
      },
      date: new Date(),
      calendar: null,
      months: moment.monthsShort()
    }
  },

  computed: {
    firstAvailableMonth () {
      let i = 0
      while (this.unavailableMonth(i)) {
        ++i
      }

      return i
    }
  },

  mounted () {
    if (this.picker.type === this.$const('DATE_TYPE_EXACT')) {
      this.loadCalendar()
    }
  },

  created () {
    if (this.value && Object.keys(this.value).length) {
      this.picker = {
        ...this.picker,
        ...this.value
      }
    }
  },

  methods: {
    processFlexibleDate () {
      let start_date = ''
      let end_date = ''

      const month = (this.picker.month + 1) < 10 ? `0${this.picker.month + 1}` : this.picker.month + 1
      start_date = `${month}-01-${this.picker.selectedYear}`

      let date = moment(start_date, 'MM-DD-YYYY')

      start_date = date.startOf('month').format(this.$const('BASE_DATE_FORMAT'))
      end_date = date.endOf('month')

      if (this.picker.flexible_type.value === '15_days_trip') {
        end_date.subtract(15, 'day')
      } else if (this.picker.flexible_type.value === '1_week_trip') {
        end_date.subtract(7, 'day')
      }

      end_date = date.format(this.$const('BASE_DATE_FORMAT'))

      // if (this.picker.flexible_type.value === 'weekend_trip') {
      //   let temp = moment(date)
      //
      //   if (temp.day() === 0) {
      //     temp.add(1, 'day')
      //   }
      //
      //   while (temp.add(1, 'day').day() !== 6) {
      //     //
      //   }
      //
      //   start_date = moment(temp)
      //   end_date = moment(temp.add(1, 'day'))
      // } else if (this.picker.flexible_type.value === '15_days_trip') {
      //   end_date = moment(date.add(14, 'day'))
      // } else {
      //   end_date = moment(date.add(6, 'day'))
      // }

      return {
        selectedYear: this.picker.selectedYear,
        month: this.picker.month,
        flexible_type: this.picker.flexible_type,
        start_date,
        end_date
      }
    },

    indexOfMonth (month) {
      return this.months.findIndex(e => {
        return e.toString() === month.toString()
      })
    },

    changeYear (value) {
      const currentYear = moment().year()

      if (value < currentYear) {
        this.picker.selectedYear = currentYear
      } else {
        this.picker.selectedYear = value
      }

      this.picker.month = this.firstAvailableMonth
    },

    onClickApply () {
      let date = {
        start_date: moment(this.picker.start).format(this.$const('BASE_DATE_FORMAT')),
        end_date: moment(this.picker.end).format(this.$const('BASE_DATE_FORMAT'))
      }

      let date_type = 'exact'
      let date_string = this.picker.date_string
      if (this.picker.type === 'flexible'
          && this.picker.flexible_type !== false
          && this.picker.month !== false) {
        date_type = this.picker.flexible_type.value

        date_string = this.months[this.picker.month]
            + ' '
            + this.picker.selectedYear
            + ' - '
            + this.picker.flexible_type.text

        date = this.processFlexibleDate()
      }

      this.$emit('input', {
        ...date,
        start: this.picker.start,
        end: this.picker.end,
        type: this.picker.type,
        month: this.picker.month,
        date_type,
        date_string
      })
    },

    onClickCancel () {
      this.$emit('hide')
    },

    onChange (selectedDates, dateStr) {
      if (selectedDates.length === 2) {
        this.picker.date_string = dateStr
        this.picker.start = selectedDates[0]
        this.picker.end = selectedDates[1]
      }
    },

    loadCalendar () {
      let config = {
        mode: 'range',
        minDate: 'today',
        inline: true,
        enableTime: false,
        conjunction: ' - ',
        dateFormat: 'D, j M',
        nextArrow: '<i class="fa fa-arrow-right"></i>',
        prevArrow: '<i class="fa fa-arrow-left"></i>'
      }

      if (this.picker.type === this.$const('DATE_TYPE_EXACT') && this.picker.start && this.picker.end) {
        config['defaultDate'] = [
          this.picker.start,
          this.picker.end
        ]
      }

      this.calendar = flatpickr('#date-picker', config)

      if (this.calendar.config) {
        this.calendar.config.onChange.push(this.onChange)
      }
    },

    showCalendar () {
      this.picker.flexible_type = false
      this.picker.month = false

      const calendarEl = document.querySelector('.flatpickr-calendar')

      if (calendarEl) {
        calendarEl.style.display = 'block'
      } else {
        this.loadCalendar()
      }
    },

    setType (type) {
      this.picker.type = type

      if (type === this.$const('DATE_TYPE_EXACT')) {
        this.showCalendar()
      } else if (type === this.$const('DATE_TYPE_FLEXIBLE')) {
        this.showMonthsList()
      }
    },

    showMonthsList () {
      const calendarEl = document.querySelector('.flatpickr-calendar')

      if (calendarEl) {
        calendarEl.style.display = 'none'
      }

      this.picker.calendar_view = false

      if (!this.picker.flexible_type) {
        this.picker.flexible_type = {
          ...this.$const('TRIP_TYPES')[0]
        }

        this.picker.month = this.firstAvailableMonth
      }
    },

    onClickTrip (index) {
      this.picker.flexible_type = this.$const('TRIP_TYPES')[index]
    },

    onClickMonth (index) {
      if (this.unavailableMonth(index)) {
        return false
      }

      this.picker.month = index
    },

    unavailableMonth (index) {
      if (this.picker.selectedYear === moment().year()) {
        return index < moment().month() || (index === moment().month() && moment().date() > 22)
      }

      return false
    }
  }
}
</script>
