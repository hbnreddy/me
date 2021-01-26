<template>
  <div class="settings">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center my-2">
        <h3 class="my-4">
          Cities Weather
        </h3>
      </div>

      <div class="d-flex justify-content-between align-items-center my-2">
        <div style="max-width: 40%;">
          <select
            v-model="cityId"
            class="form-control mr-3"
            @change="onChangeCity()"
          >
            <option
              value="false"
              selected
            >
              No city
            </option>

            <option
              v-for="cityObj in cities.enabled"
              :key="cityObj.id"
              :value="cityObj.id"
              selected
            >
              {{ cityObj.name }}
            </option>
          </select>
        </div>

        <a
          v-if="cityId"
          href="#"
          class="btn btn-success text-white ml-2"
          @click.prevent="save()"
        >Save changes</a>
      </div>

      <table
        v-if="cityId"
        class="table"
      >
        <thead>
          <tr>
            <th scope="col" />
            <th scope="col">
              High
            </th>
            <th scope="col">
              Low
            </th>
            <th scope="col">
              Average
            </th>
            <th scope="col">
              Precipitation
            </th>
            <th scope="col">
              Best time
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(row, key, index) in city"
            :key="index"
          >
            <th scope="row">
              {{ capitalize(key) }}
            </th>

            <td>
              <input
                v-model="city[key].high"
                type="text"
                class="form-control-plaintext input-text-number"
              >
            </td>

            <td>
              <input
                v-model="city[key].low"
                type="text"
                class="form-control-plaintext input-text-number"
              >
            </td>

            <td>
              <input
                v-model="city[key].average"
                type="text"
                class="form-control-plaintext input-text-number"
              >
            </td>

            <td>
              <input
                v-model="city[key].precipitation"
                type="text"
                class="form-control-plaintext input-text-number"
              >
            </td>

            <td>
              <input
                v-model="city[key].best_time"
                class="form-check-input toggle-checkbox"
                type="checkbox"
              >
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import axios from 'axios'
import {ENABLED} from '../states'

export default {
  data () {
    return {
      cityId: false,
      months: [
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december'
      ],
      city: {
      }
    }
  },

  computed: {
    ...mapGetters([
      'cities'
    ])
  },

  async created () {
    this.$store.commit('setLoading', true)

    this.fetchCities()

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchCities () {
      await this.$store.dispatch('fetchCities', {
        state: ENABLED
      })
    },

    async onChangeCity () {
      this.$store.commit('setLoading', true)

      await axios
        .get(`/api/city-weathers/${this.cityId}/weathers`)
        .then(r => {
          if (r.data.success) {
            this.city = r.data.entities
          } else {
            this.resetCity()
          }
        })

      this.$store.commit('setLoading', false)
    },

    async save () {
      this.$store.commit('setLoading', true)

      await axios
        .post(`/api/city-weathers/${this.cityId}/set-weathers`, {
          weathers: this.city
        })

      this.$store.commit('setLoading', false)
    },

    resetCity () {
      this.months.forEach(month => {
        this.city = {
          ...this.city,
          [month]: {
            month,
            high: 0,
            low: 0,
            average: 0,
            precipitation: 0,
            best_time: false
          }
        }
      })
    },

    capitalize (string) {
      return string.toString().toUpperCase()[0] + string.toString().substring(1, string.length)
        .toLowerCase()
    }
  }
}
</script>
