<template>
  <div class="traveller-form-block">
    <form class="traveller-form">
      <div class="form-row mb-3">
        <p class="title">
          *{{ __('Kindly enter the details as mentioned in the passport') }}
        </p>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="first-name"
              class="col-sm-5 col-form-label"
            >{{ __('First Name') }}</label>

            <div class="col-sm-7">
              <input
                id="first-name"
                v-model="traveller.first_name"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('first_name')" class="error-message">
                {{ errors.first_name[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              for="middle-name"
              class="col-sm-5 col-form-label"
            >{{ __('Middle Name') }}</label>

            <div class="col-sm-7">
              <input
                id="middle-name"
                v-model="traveller.middle_name"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('middle_name')" class="error-message">
                {{ errors.middle_name[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="last-name"
              class="col-sm-5 col-form-label"
            >{{ __('Last Name') }}</label>

            <div class="col-sm-7">
              <input
                id="last-name"
                v-model="traveller.last_name"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('last_name')" class="error-message">
                {{ errors.last_name[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              for="email"
              class="col-sm-5 col-form-label"
            >{{ __('Email ID') }}</label>

            <div class="col-sm-7">
              <input
                id="email"
                v-model="traveller.email"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('email')" class="error-message">
                {{ errors.email[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              :for="'birthdate'"
              class="col-sm-5 col-form-label"
            >{{ __('Date of birth') }}</label>

            <div class="col-sm-7">
              <date-input
                :id="'birthdate'"
                v-model="traveller.birthdate"
              />

              <div v-if="hasErrors('birthdate')" class="error-message">
                {{ errors.birthdate[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              :for="'passport-start-date'"
              class="col-sm-5 col-form-label"
            >{{ __('Passport Start Date') }}</label>

            <div class="col-sm-7">
              <date-input
                :id="'passport-start-date'"
                v-model="traveller.passport_start_date"
              />

              <div v-if="hasErrors('passport_start_date')" class="error-message">
                {{ errors.passport_start_date[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="gender"
              class="col-sm-5 col-form-label"
            >{{ __('Gender') }}</label>

            <div class="col-sm-7">
              <div class="position-relative small-input">
                <select
                  id="gender"
                  v-model="traveller.gender"
                  class="custom-select form-control-sm small-input"
                  required
                  style="background: none"
                >
                  <option value="male" selected>
                    {{ __('Male') }}
                  </option>

                  <option value="female">
                    {{ __('Female') }}
                  </option>
                </select>

                <i class="fa fa-angle-down" />
              </div>

              <div v-if="hasErrors('gender')" class="error-message">
                {{ errors.gender[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              :for="'passport-end-date'"
              class="col-sm-5 col-form-label"
            >{{ __('Passport End Date') }}</label>

            <div class="col-sm-7">
              <date-input
                :id="'passport-end-date'"
                v-model="traveller.passport_end_date"
              />

              <div v-if="hasErrors('passport_end_date')" class="error-message">
                {{ errors.passport_end_date[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="nationality"
              class="col-sm-5 col-form-label"
            >{{ __('Nationality') }}</label>

            <div class="col-sm-7">
              <div class="position-relative small-input">
                <select
                  id="nationality"
                  v-model="traveller.nationality"
                  class="custom-select form-control-sm small-input"
                  style="background: none"
                >
                  <option value="0" />
                  <option value="romania">
                    Romania
                  </option>
                  <option value="china">
                    China
                  </option>
                  <option value="uk">
                    UK
                  </option>
                  <option value="sua">
                    SUA
                  </option>
                </select>

                <i class="fa fa-angle-down" />
              </div>

              <div v-if="hasErrors('nationality')" class="error-message">
                {{ errors.nationality[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="mobile-number"
              class="col-sm-5 col-form-label"
            >{{ __('Mobile Number') }}</label>

            <div class="col-sm-7">
              <div class="mobile-input">
                <div class="position-relative">
                  <select
                    class="custom-select form-control-sm phone-prefix-select"
                    style="background: none; padding-right: 16px;"
                  >
                    <option selected>
                      +91
                    </option>
                    <option value="1">
                      +40
                    </option>
                    <option value="2">
                      +44
                    </option>
                  </select>

                  <i
                    class="fa fa-angle-down"
                    style="right: 8px;"
                  />
                </div>

                <input
                  id="mobile-number"
                  v-model="traveller.mobile_number"
                  type="text"
                  class="form-control form-control-sm mobile-number"
                  required
                >
              </div>

              <div v-if="hasErrors('mobile_number')" class="error-message">
                {{ errors.mobile_number[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <p class="section-title">
        {{ __('Address') }}
      </p>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="house-number"
              class="col-sm-5 col-form-label"
            >{{ __('House/Flat no') }}</label>

            <div class="col-sm-7">
              <input
                id="house-number"
                v-model="traveller.house_number"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('house_number')" class="error-message">
                {{ errors.house_number[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              for="street-number"
              class="col-sm-5 col-form-label"
            >{{ __('Street/Road no') }}</label>

            <div class="col-sm-7">
              <input
                id="street-number"
                v-model="traveller.street_number"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('street_number')" class="error-message">
                {{ errors.street_number[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="area"
              class="col-sm-5 col-form-label"
            >{{ __('Area') }}</label>

            <div class="col-sm-7">
              <input
                id="area"
                v-model="traveller.area"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('area')" class="error-message">
                {{ errors.area[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              for="city"
              class="col-sm-5 col-form-label"
            >{{ __('City') }}/{{ __('Town') }}</label>

            <div class="col-sm-7">
              <input
                id="city"
                v-model="traveller.city"
                type="text"
                class="form-control form-control-sm"
                required
              >

              <div v-if="hasErrors('city')" class="error-message">
                {{ errors.city[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group row">
        <div class="col-sm-6">
          <div class="row">
            <label
              for="country"
              class="col-sm-5 col-form-label"
            >{{ __('Country') }}</label>

            <div class="col-sm-7">
              <div class="position-relative small-input">
                <select
                  id="country"
                  v-model="traveller.country"
                  class="custom-select form-control-sm small-input"
                  required
                  style="background: none"
                >
                  <option
                    value="romania"
                    selected
                  >
                    Romania
                  </option>
                  <option value="usa">
                    USA
                  </option>
                  <option value="uk">
                    UK
                  </option>
                  <option value="france">
                    France
                  </option>
                </select>

                <i class="fa fa-angle-down" />
              </div>

              <div v-if="hasErrors('country')" class="error-message">
                {{ errors.country[0] }}
              </div>
            </div>
          </div>
        </div>

        <div class="col-sm-6">
          <div class="row">
            <label
              for="pincode"
              class="col-sm-5 col-form-label"
            >{{ __('Pincode') }}</label>

            <div class="col-sm-7">
              <input
                id="pincode"
                v-model="traveller.pincode"
                type="text"
                class="form-control form-control-sm small-input"
                required
              >

              <div v-if="hasErrors('pincode')" class="error-message">
                {{ errors.pincode[0] }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <div class="form-row mb-3 d-flex">
          <span
            v-if="main"
            class="text-success font-weight-bold"
          >
            {{ __('Main Customer') }}
          </span>
        </div>

        <a
          href="#"
          class="btn btn-outline-dark btn-add-traveller d-flex justify-content-center align-items-center"
          @click.prevent="update()"
        >
          <loading-circle
            :loading="loading"
            :small="true"
            class="mr-2"
          />

          <span v-if="!completed">{{ __('Add traveller') }}</span>
          <span v-else>{{ __('Update traveller') }}</span>
        </a>
      </div>
    </form>
  </div>
</template>

<script>
import DateInput from '../form/DateInput'

import moment from 'moment'
import LoadingCircle from '../loading/LoadingCircle'

export default {
  components: {
    LoadingCircle,
    DateInput
  },

  props: {
    traveller: {
      type: Object,
      required: true
    },

    loading: {
      type: Boolean,
      default: false
    },

    completed: {
      type: Boolean,
      default: false
    },

    main: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      errors: {
      }
    }
  },

  methods: {
    hasErrors (key) {
      return this.errors[key] && this.errors[key].length
    },

    isValidEmail (email) {
      const regExp = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/

      return regExp.test(email)
    },

    checkDate (date, minYear = false, maxYear = false) {
      const momentDate = moment(date, 'DD-MM-YYYY')

      let success = momentDate.isValid()

      if (minYear) {
        success = success && momentDate.year() >= minYear
      }

      if (maxYear) {
        success = success && momentDate.year() <= maxYear
      }

      return success
    },

    validate () {
      this.errors = {
        //
      }

      const excludeKeys = [
        // 'middle_name',
        'nickname',
        'total_payment',
        'main_customer'
      ]

      Object.keys(this.traveller).forEach(key => {
        this.errors[key] = this.errors[key] ? this.errors[key] : []
      })

      let foundErrors = false
      Object.keys(this.traveller).forEach(key => {
        if (!excludeKeys.includes(key) && (
          this.traveller.type === 'adults'
          || (this.traveller.type !== 'adults')
        )) {
          if (!this.traveller[key] && !excludeKeys.includes(key)) {
            foundErrors = true
            this.errors[key].push('This field is required.')
          }
        }
      })

      if (foundErrors) {
        return false
      }

      if (!/^[a-z]+$/i.test(this.traveller.first_name)) {
        this.errors['first_name'].push('Invalid first name.')
      }

      if (!/^[a-z]+$/i.test(this.traveller.middle_name) && !excludeKeys.includes('middle_name')) {
        this.errors['middle_name'].push('Invalid middle name.')
      }

      if (!/^[a-z]+$/i.test(this.traveller.last_name)) {
        this.errors['last_name'].push('Invalid last name.')
      }

      if (!/^[a-z]+$/i.test(this.traveller.city)) {
        this.errors['city'].push('Invalid city.')
      }

      if (!/^[a-z]+$/i.test(this.traveller.area)) {
        this.errors['area'].push('Invalid area.')
      }

      if (this.traveller.type === 'adults'
        && !this.isValidEmail(this.traveller.email) && !excludeKeys.includes('email')) {
        this.errors['email'].push('Invalid e-mail.')
      }

      if (this.traveller.type === 'adults' && !/^\d{9}$/.test(this.traveller.mobile_number)) {
        this.errors['mobile_number'].push('Invalid phone number.')
      }

      if (!/^\d+$/.test(this.traveller.street_number)) {
        this.errors['street_number'].push('Invalid street number.')
      }

      if (!/^\d+$/.test(this.traveller.house_number)) {
        this.errors['house_number'].push('Invalid house number.')
      }

      if (!/^[0-9a-zA-Z]+$/i.test(this.traveller.pincode)) {
        this.errors['pincode'].push('Invalid pincode.')
      }

      this.traveller.birthdate = this.traveller.birthdate.replace(/\s/g, '')
      if (! this.checkDate(this.traveller.birthdate)) {
        this.errors['birthdate'].push('Invalid date.')
      }

      this.traveller.passport_start_date = this.traveller.passport_start_date.replace(/\s/g, '')
      if (! this.checkDate(this.traveller.passport_start_date)) {
        this.errors['passport_start_date'].push('Invalid date.')
      }

      this.traveller.passport_end_date = this.traveller.passport_end_date.replace(/\s/g, '')
      if (! this.checkDate(this.traveller.passport_end_date, moment().year())) {
        this.errors['passport_end_date'].push('Invalid date.')
      }

      return !Object.values(this.errors).some((error) => {
        return error.length !== 0
      })
    },

    update () {
      if (!this.validate()) {
        return false
      }

      const next = !this.completed

      this.$emit('update', next)
    }
  }
}
</script>
