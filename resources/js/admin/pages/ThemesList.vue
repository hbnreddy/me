<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-4">
        Themes
      </h3>

      <a
        href="#"
        class="btn btn-primary text-white"
        @click.prevent="showAddForm()"
      >New theme</a>
    </div>

    <form
      v-if="showForm"
      class="mb-3"
    >
      <div class="form-group">
        <label for="name_input">Theme name</label>

        <input
          id="name_input"
          v-model="theme.name"
          type="text"
          class="form-control"
          placeholder="Enter a name"
        >
      </div>

      <div class="form-group">
        <label for="description_input">Theme description</label>

        <input
          id="description_input"
          v-model="theme.description"
          type="text"
          class="form-control"
          placeholder="Describe the theme"
        >
      </div>

      <div
        v-if="error"
        style="color: crimson;"
        class="mb-2"
      >
        {{ error }}
      </div>

      <button
        type="submit"
        class="btn btn-success"
        @click.prevent="onSave()"
      >
        Save theme
      </button>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">
            #
          </th>
          <th scope="col">
            Name
          </th>
          <th scope="col">
            Description
          </th>
          <th scope="col" />
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(themeObj, index) in themes"
          :key="themeObj.id"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>

          <td>
            <a href="#">
              {{ themeObj.name }}
            </a>
          </td>

          <td>
            <a href="#">
              {{ themeObj.description ? themeObj.description : '-' }}
            </a>
          </td>

          <td>
            <a
              href="#"
              @click.prevent="onUpdate(index)"
            >Edit</a>
            <a
              href="#"
              @click.prevent="onDelete(index)"
            >Delete</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
  data () {
    return {
      error: false,
      showForm: false,
      theme: {
        id: false,
        name: '',
        description: ''
      }
    }
  },

  computed: {
    ...mapGetters([
      'themes'
    ])
  },

  async created () {
    this.$store.commit('setLoading', true)

    if (!this.themes.length) {
      await this.$store.dispatch('fetchThemes')
    }

    this.$store.commit('setLoading', false)
  },

  methods: {
    showAddForm () {
      this.showForm = true
    },

    onUpdate (index) {
      this.theme = {
        ...this.themes[index]
      }

      this.showAddForm()
    },

    async onDelete (index) {
      this.$store.commit('setLoading', true)

      await this.$store.dispatch('deleteTheme', {
        id: this.themes[index].id
      })

      let themes = [...this.themes]

      themes.splice(index, 1)

      await this.$store.commit('setThemes', themes)

      this.$store.commit('setLoading', false)
    },

    async onSave () {
      this.error = false

      if (!this.theme.name.length) {
        this.error = 'You must fill in at least the name property.'

        return false
      }

      this.$store.commit('setLoading', true)

      let temp = await this.$store.dispatch('storeTheme', {
        theme: this.theme
      })

      if (temp) {
        let themes = [...this.themes]

        if (this.theme.id !== false) {
          const index = this.themes.findIndex(e => e.id === this.theme.id)
          themes.splice(index, 1, temp)
        } else {
          themes.push(temp)
        }

        await this.$store.commit('setThemes', themes)
      }

      this.theme = {
        id: false,
        name: '',
        description: ''
      }

      this.showForm = false
      this.$store.commit('setLoading', false)
    }
  }
}
</script>
