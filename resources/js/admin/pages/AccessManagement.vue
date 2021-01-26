<template>
  <div class="access-codes-page">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center my-2">
        <h3 class="my-4">
          Access Management
        </h3>
      </div>

      <form class="form-inline mb-2">
        <div class="form-group mx-sm-3 mb-2">
          <label for="code" class="sr-only">Code</label>

          <input
            id="code"
            v-model="storeItem.code"
            type="text"
            class="form-control"
            placeholder="Access code"
          >
        </div>

        <div class="form-group mx-sm-3 mb-2">
          <select
            v-model="storeItem.expire_date"
            class="form-control"
          >
            <option
              v-for="(option, index) in dateOptions"
              :key="index"
              :value="option.value"
            >
              {{ option.text }}
            </option>
          </select>
        </div>

        <div class="form-group mx-sm-2 mb-2">
          <div class="custom-control custom-checkbox">
            <input
              id="checkbox"
              v-model="storeItem.forever"
              type="checkbox"
              class="custom-control-input"
            >

            <label
              for="checkbox"
              class="custom-control-label"
            >
              Forever
            </label>
          </div>
        </div>

        <button
          type="submit"
          class="btn btn-primary mb-2 mx-4"
          @click.prevent="store()"
        >
          Create
        </button>
      </form>

      <div
        v-if="errorMessage"
        class="text-danger mb-4 mx-4"
      >
        {{ errorMessage }}
      </div>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">
              #
            </th>
            <th scope="col">
              Code
            </th>
            <th scope="col">
              Expire on
            </th>
            <th scope="col">
              Forever
            </th>
            <th scope="col">
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(item, index) in items"
            :key="index"
          >
            <th scope="row">
              {{ index + 1 }}
            </th>
            <td>{{ item.code }}</td>
            <td>{{ printDate(item) }}</td>
            <td>
              <div class="custom-control custom-checkbox">
                <input
                  :id="`checkbox-${index}`"
                  v-model="item.forever"
                  type="checkbox"
                  class="custom-control-input"
                >

                <label
                  :for="`checkbox-${index}`"
                  class="custom-control-label"
                />
              </div>
            </td>

            <td>
              <a
                href="#"
                class="btn btn-outline-danger"
                @click.prevent="deleteItem(index)"
              >Delete
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import * as moment from 'moment'

export default {
  data () {
    return {
      errorMessage: '',
      dateOptions: [
        {
          text: '1 hour',
          value: 1
        },
        {
          text: '6 hours',
          value: 6
        },
        {
          text: '12 hours',
          value: 12
        },
        {
          text: '24 hours',
          value: 24
        },
        {
          text: '1 week',
          value: 24 * 7
        }
      ],
      storeItem: {
        //
      },
      items: [
        {
          code: '123',
          expire_date: 'Now',
          forever: false
        }
      ]
    }
  },

  created () {
    this.$store.commit('setLoading', true)

    this.init()
    this.fetchCodes()

    this.$store.commit('setLoading', false)
  },

  methods: {
    printDate (item) {
      if (item.forever) {
        return '-'
      }

      return moment(item.date).format('LLL')
    },

    async fetchCodes () {
      await axios
        .get(`/api/access-codes`)
        .then(r => {
          this.items = r.data.entities
        })
    },

    init () {
      this.storeItem = {
        code: '',
        expire_date: '',
        forever: false
      }
    },

    async store () {
      if (!this.storeItem.code || (!this.storeItem.expire_date && !this.storeItem.forever)) {
        this.errorMessage = 'Fields required.'

        return false
      }

      this.errorMessage = ''

      const date = moment()
        .add(this.storeItem.expire_date, 'hours')
        .format('YYYY-MM-DD HH:mm:ss')

      await axios
        .post(`/api/access-codes/store`, {
          ...this.storeItem,
          expire_date: date
        })
        .then(r => {
          this.init()

          this.items.push(r.data.entity)
        })
    },

    async deleteItem (index) {
      await axios
        .post(`/api/access-codes/${this.items[index].id}/delete`)
        .then(r => {
          if (r.data.success) {
            this.items.splice(index, 1)
          }
        })
    }
  }
}
</script>
