<template>
  <div class="container">
    <div class="d-flex justify-content-between align-items-center my-2">
      <h3 class="my-2">
        Sygic Tags
      </h3>

      <toggle-input
        :enabled="showAssigned"
        :show-text="true"
        :left-text="'Unassigned'"
        :right-text="'Assigned'"
        @input="onToggleShowAssigned()"
      />
    </div>

    <div class="d-flex justify-content-between align-items-center my-2">
      <div
        class="d-flex justify-content-between align-items-center mr-2"
        style="flex: 2;"
      >
        <div class="mr-3">
          <i
            class="fa fa-search"
            style="font-size: 20px;"
          />
        </div>

        <input
          v-model="searchValue"
          class="form-control form-control-borderless mr-2"
          type="search"
          placeholder="Search tags or keywords"
        >

        <a
          href="#"
          class="btn btn-success text-white"
          @click.prevent="search()"
        >Search
        </a>
      </div>

      <div class="d-flex justify-content-end align-items-center my-2">
        <a
          v-if="showImportRow"
          href="#"
          class="btn btn-danger text-white"
          @click.prevent="showImport()"
        >Hide this
        </a>

        <a
          v-else
          href="#"
          class="btn btn-primary text-white"
          @click.prevent="showImport()"
        >Import tags
        </a>

        <a
          v-if="hasChanges"
          href="#"
          class="btn btn-success text-white ml-2"
          @click.prevent="onSave()"
        >Save changes</a>
      </div>
    </div>

    <form
      v-if="showImportRow"
      :action="'/tags/import'"
      method="post"
      class="d-flex justify-content-between align-items-center my-2"
      enctype="multipart/form-data"
    >
      <input
        :value="csrf()"
        type="hidden"
        name="_token"
      >

      <div class="form-group">
        <label for="import-file">Import tags</label>
        <input
          id="import-file"
          name="import_file"
          type="file"
          class="form-control-file"
        >
      </div>

      <button
        type="submit"
        class="btn btn-primary text-white"
      >
        Import
      </button>
    </form>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">
            #
          </th>
          <th scope="col">
            Key
          </th>
          <th scope="col" />
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="(tag, index) in filteredTags"
          :key="index"
        >
          <th scope="row">
            {{ index + 1 }}
          </th>
          <td>
            <a href="#">
              {{ tag.tag_key }}
            </a>
          </td>
          <td>
            <select
              v-model="filteredTags[index].category_id"
              class="form-control"
              @change="onChange(tag.id)"
            >
              <option
                v-if="!categories.length"
                :value="parseInt(0)"
                selected
              >
                No available categories
              </option>
              <option :value="parseInt(0)">
                None
              </option>

              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>

    <paginate
      v-if="totalCount > count"
      :page-count="pagesCount"
      :click-handler="onChangePage"
      :prev-text="'Previous'"
      :next-text="'Next'"
      :page-range="3"
      :margin-pages="2"
      :container-class="'pagination'"
      :page-class="'page-item'"
    />
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import {cloneDeep} from 'lodash'

export default {
  data () {
    return {
      totalCount: 0,
      offset: 0,
      count: 10,
      searchValue: '',
      localTags: [],
      showImportRow: false,
      showAssigned: true,
      changes: {
      },
      hasChanges: false,
      tag: {
        id: false,
        category_id: '',
        tag_key: ''
      }
    }
  },

  computed: {
    ...mapGetters([
      'categories',
      'tags'
    ]),

    pagesCount () {
      return Math.ceil(this.totalCount / this.count)
    },

    filteredTags () {
      if (this.showAssigned) {
        return this.localTags.filter(tag => {
          return parseInt(tag.category_id) !== 0
        })
      }

      return this.localTags.filter(tag => {
        return parseInt(tag.category_id) === 0
      })
    }
  },

  async created () {
    // return false

    this.$store.commit('setLoading', true)

    if (!this.categories.length) {
      await this.$store.dispatch('fetchCategories')
    }

    if (!this.tags.length) {
      const result = await this.$store.dispatch('fetchTags', {
        offset: this.offset,
        count: this.count,
        search: this.searchValue,
        assigned: this.showAssigned ? 1 : 0
      })

      this.totalCount = result.total_count
    }

    this.localTags = cloneDeep(this.tags)

    this.$store.commit('setLoading', false)
  },

  methods: {
    async onToggleShowAssigned () {
      this.$store.commit('setLoading', true)

      this.offset = 0
      this.searchValue = ''

      this.showAssigned = !this.showAssigned

      const result = await this.$store.dispatch('fetchTags', {
        offset: this.offset,
        count: this.count,
        search: this.searchValue,
        assigned: this.showAssigned ? 1 : 0
      })

      this.totalCount = result.total_count

      this.localTags = cloneDeep(this.tags)

      this.$store.commit('setLoading', false)
    },

    onChange (id) {
      this.changes[id] = {
        ...this.localTags.find(e => parseInt(e.id) === id)
      }
      this.hasChanges = true
    },

    async onSave () {
      this.$store.commit('setLoading', true)

      await this.$store.dispatch('storeTags', {
        tags: this.changes
      })

      this.hasChanges = false

      await this.$store.commit('setTags', [...this.tags, ...this.localTags])

      this.$store.commit('setLoading', false)
    },

    showImport () {
      this.showImportRow = !this.showImportRow
    },

    async onChangePage (pageNumber) {
      this.$store.commit('setLoading', true)

      this.offset = (pageNumber - 1) * this.count

      const result = await this.$store.dispatch('fetchTags', {
        offset: this.offset,
        count: this.count,
        search: this.searchValue,
        assigned: this.showAssigned ? 1 : 0
      })

      this.totalCount = result.total_count

      this.localTags = cloneDeep(this.tags)

      this.$store.commit('setLoading', false)
    },

    async search () {
      this.$store.commit('setLoading', true)

      const result = await this.$store.dispatch('fetchTags', {
        offset: this.offset,
        count: this.count,
        search: this.searchValue,
        assigned: this.showAssigned ? 1 : 0
      })

      this.totalCount = result.total_count

      this.localTags = cloneDeep(this.tags)

      this.$store.commit('setLoading', false)
    },

    csrf () {
      return document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  }
}
</script>
