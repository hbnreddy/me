<template>
  <div class="language-management">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center my-2">
        <h3 class="my-4">
          Language Management
        </h3>
      </div>

      <div class="languages">
        <div
          v-for="(language, index) in languages"
          :key="index"
          :class="{ 'active': active === index }"
          class="card language-card"
          @click.prevent="onClickLanguage(index)"
        >
          {{ language.code.toUpperCase() }}
        </div>
      </div>

      <div class="d-flex justify-content-between mb-4">
        <div>
          <input
            v-model="newKey"
            type="text"
            class="mr-3"
            style="padding: 4px 6px; border: 1px solid rgba(0, 0, 0, 0.125); border-radius: 4px;"
            placeholder="key"
          >

          <a
            href="#"
            class="btn btn-primary"
            @click.prevent="createKey()"
          >
            Create New Key
          </a>
        </div>

        <a
          href="#"
          class="btn btn-success"
          @click.prevent="save()"
        >
          Save changes
        </a>
      </div>

      <table v-if="languages.length" class="table">
        <thead>
          <tr>
            <th scope="col">
              #
            </th>
            <th scope="col">
              Key (The text from page)
            </th>
            <th scope="col">
              Value (Text appears on page)
            </th>
            <th scope="col">
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(value, key, index) in locales[languages[active].code]"
            :key="index"
            class="text-center"
            scope="col"
          >
            <td class="text-left">
              {{ index }}
            </td>

            <td class="text-left">
              {{ key }}
            </td>

            <td class="text-left">
              <input
                v-model="locales[languages[active].code][key]"
                type="text"
                style="padding: 4px 6px; border: 1px solid rgba(0, 0, 0, 0.125); border-radius: 4px;"
                placeholder="Value"
              >
            </td>

            <td class="text-right">
              <a
                class="btn btn-danger text-light"
                @click.prevent="remove(key)"
              >
                Remove
              </a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import axios from 'axios'

export default {
  data () {
    return {
      newKey: '',
      active: 0,
      languages: [],
      locales: {
        //
      }
    }
  },

  computed: {
    ...mapGetters([
      'loading'
    ])
  },

  created () {
    this.$store.commit('setLoading', true)

    this.fetchLanguages()
    this.fetchLocales()

    this.$store.commit('setLoading', false)
  },

  methods: {
    createKey () {
      Object.keys(this.locales).forEach(locale => {
        this.locales[locale] = {
          ...this.locales[locale],
          [this.newKey.toLowerCase().replace(/ /g, '_')]: ''
        }
      })

      this.newKey = ''

      this.save()
    },

    remove (key) {
      Object.keys(this.locales).forEach(locale => {
        delete this.locales[locale][key]
      })

      this.save()
    },

    async save () {
      this.$store.commit('setLoading', true)
      await axios
        .post(`/admin/locales/store`, {
          locales: this.locales
        })
        .then(r => {
          if (r.data.success) {
            this.locales = r.data.entities
          }

          this.$store.commit('setLoading', false)
        })
    },

    onClickLanguage (index) {
      this.active = index
    },

    async fetchLanguages () {
      await axios
        .get(`/api/languages`)
        .then(r => {
          this.languages = r.data.entities
        })
    },

    async fetchLocales () {
      await axios
        .get(`/admin/locales`)
        .then(r => {
          if (r.data.success) {
            this.locales = r.data.entities
          }
        })
    }
  }
}
</script>
