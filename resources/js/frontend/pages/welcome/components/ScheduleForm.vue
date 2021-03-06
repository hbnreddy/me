<template>
  <form
    ref="form"
    method="GET"
    class="schedule-form"
    autocomplete="off"
    @keydown.enter.prevent=""
  >
    <input
      :value="csrf"
      type="hidden"
    >

    <city-input
      @input="selectCity($event)"
    />

    <div class="form-row">
      <div class="col-md-8">
        <div class="form-group">
          <input
            :value="query.date.date_string"
            type="text"
            class="form-control"
            placeholder="A week in Month"
            @click.prevent="showDatePicked()"
          >

          <i class="fa fa-calendar-o icon" />

          <travel-date-picker
            v-if="datePicker"
            v-on-clickaway="onClickOutsideDatePicker"
            :value="query.date"
            @input="onDatePickerInput($event)"
            @hide="hideDatePicker"
          />
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <input
            :value="travellersInputText()"
            type="text"
            class="form-control"
            placeholder="Travellers"
            @click.prevent="showTravellersPicker()"
          >

          <i class="fa fa-user icon" />

          <travellers-picker
            v-if="travellersPicker"
            v-on-clickaway="onClickOutsideTravellersPicker"
            :value="query.travellers"
            @input="onTravellersInput($event)"
            @hide="hideTravellersPicker()"
          />
        </div>
      </div>

      <div class="col-md-12">
        <div class="form-group position-relative">
          <input
            :value="themesInputText()"
            type="text"
            class="form-control"
            placeholder="Theme"
            @click.prevent="showThemePicker()"
          >

          <i class="fa fa-th-large icon" />

          <theme-picker
            v-if="themePicker"
            v-on-clickaway="onClickOutsideThemePicker"
            :themes="themes"
            :selected="query.themes"
            @input="onThemesInput($event)"
            @hide="hideThemePicker()"
          />
        </div>
      </div>

      <div
        v-if="errorMessage"
        class="error-message"
      >
        *{{ errorMessage }}
      </div>

      <div class="col-md-12 text-right">
        <button
          type="submit"
          class="btn btn-orange blob orange"
          @keydown.enter.prevent=""
          @click.prevent="explore()"
        >
          {{ __('Start exploring') }}
        </button>
      </div>
    </div>
  </form>
</template>

<script>
import { cloneDeep } from 'lodash'
import {mixin as clickaway} from 'vue-clickaway'
import TravelDatePicker from '../../../components/TravelDatePicker'
import ThemePicker from '../../../components/ThemePicker'
import TravellersPicker from '../../../components/TravellersPicker'
import CityInput from './CityInput'

export default {
  components: {
    CityInput,
    TravellersPicker,
    ThemePicker,
    TravelDatePicker
  },

  mixins: [
    clickaway
  ],

  props: {
    staticQuery: {
      type: Object | Boolean,
      required: true
    },

    staticThemes: {
      type: Array,
      default: () => []
    },

    async: {
      type: Boolean,
      default: () => false
    },

    error: {
      type: String | Boolean,
      default: () => false
    }
  },

  data () {
    return {
      errorMessage: false,
      query: {
        themes: [],
        travellers: {
          adults: 0,
          children: 0,
          infants: 0
        },
        date: false,
        origin_city_id: false
      },
      themes: [],
      themePicker: false,
      datePicker: false,
      travellersPicker: false
    }
  },

  computed: {
    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  },

  created () {
    if (this.staticQuery) {
      this.query = cloneDeep(this.staticQuery)
    }

    this.errorMessage = this.error

    this.themes = cloneDeep(this.staticThemes)
  },

  methods: {
    selectCity (id) {
      this.query.origin_city = id
    },

    themesInputText () {
      const count = Object.keys(this.query.themes).length

      return count ? count + ' Themes' : ''
    },

    onThemesInput (values) {
      this.query.themes = cloneDeep(values)
    },

    showThemePicker () {
      this.themePicker = true
    },

    hideThemePicker () {
      this.themePicker = false
    },

    onClickOutsideThemePicker () {
      this.themePicker = false
    },

    onClickOutsideTravellersPicker () {
      this.travellersPicker = false
    },

    onTravellersInput (value) {
      this.query.travellers = {
        ...value
      }
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    onDatePickerInput (value) {
      this.query.date = value

      this.hideDatePicker()
    },

    onClickOutsideDatePicker () {
      this.datePicker = false
    },

    showTravellersPicker () {
      this.travellersPicker = true
    },

    countTravellers () {
      return this.query.travellers.adults
          + this.query.travellers.children
          + this.query.travellers.infants
    },

    showDatePicked () {
      this.datePicker = true
    },

    hideDatePicker () {
      this.datePicker = false
    },

    travellersInputText () {
      const count = this.countTravellers()

      return count
        ? count.toString() + '  Traveller' + (count > 1
          ? 's'
          : ''
        )
        : ''
    },

    validateForm () {
      let validate = {
        success: true,
        error: ''
      }

      if (!this.query.origin_city) {
        validate.success = false
        validate.error = 'Please the origin city from where you start.'

        return validate
      }

      if (!this.query.date.date_string) {
        validate.success = false
        validate.error = 'Please select the date range.'

        return validate
      }

      const travellersCount = this.query.travellers.adults
          + this.query.travellers.children
          + this.query.travellers.infants

      if (!travellersCount) {
        validate.success = false
        validate.error = 'Please choose number of travellers.'

        return validate
      }

      if (!this.query.travellers.adults) {
        validate.success = false
        validate.error = 'At least one adult is required.'

        return validate
      }

      if (!this.query.themes.length) {
        validate.success = false
        validate.error = 'Please select at least 1 theme to proceed.'

        return validate
      }

      return validate
    },

    async explore () {
      const validate = this.validateForm()

      if (!validate.success) {
        this.errorMessage = validate.error

        return false
      }

      if (!this.async) {
        this.$redirect('explore', {
          currency: 'usd',
          start_date: this.query.date.start_date,
          end_date: this.query.date.end_date,
          origin_city_id: this.query.origin_city,
          date_type: this.query.date.date_type,
          themes: this.query.themes.join('-'),
          travellers: Object.values(this.query.travellers).join('-')
        })
      }

      return true
    }
  }
}
</script>
