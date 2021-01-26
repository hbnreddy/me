<template>
  <div
    class="search-input-box city-input"
    @keydown.up="onKeyUp()"
    @keydown.down="onKeyDown()"
    @keydown.enter="selectPlace(index, $event)"
  >
    <div class="form-group">
      <input
        v-if="city !== false"
        :value="city.id"
        type="hidden"
      >

      <input
        id="location"
        ref="cityInput"
        v-model="city.full_name"
        :style="inputStyle"
        :disabled="disabled"
        type="text"
        class="form-control input-city"
        aria-describedby="location"
        placeholder="Where are you starting from?"
        @input="searchCity($event.target.value)"
        @focusin="onFocusIn()"
        @focusout="onFocusOut()"
      >

      <i v-if="showIcon" class="fa fa-map-marker icon" />

      <i
        v-if="!disabled && city.id"
        class="fa fa-times icon-close"
        @click.prevent="removeCity()"
      />
    </div>

    <div
      v-if="showResultsDropdown && searchResults.length"
      v-on-clickaway="onClickOutside"
      class="dropdown-menu dropdown-results"
    >
      <ul
        ref="resultsList"
        class="results-list"
      >
        <li
          v-for="(result, resultIndex) in searchResults"
          :key="resultIndex"
          :class="{ active: index === resultIndex }"
          class="results-list-item"
        >
          <a
            href="#"
            @click.prevent="selectPlace(resultIndex)"
          >
            {{ result.full_name }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import {debounce} from 'lodash'
import axios from 'axios'
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Object,
      default: () => null
    },

    showIcon: {
      type: Boolean,
      default: true
    },

    disabled: {
      type: Boolean,
      default: false
    },

    inputStyle: {
      type: String,
      default: () => ''
    }
  },

  data () {
    return {
      cursorTextInterval: false,
      index: false,
      showResultsDropdown: false,
      searchInput: '',
      city: {
        full_name: ''
      },
      searchResults: []
    }
  },

  created () {
    if (this.value) {
      this.city = {
        id: this.value.id,
        full_name: this.value.name_suffix + ', ' + this.value.name
      }
    }
  },

  methods: {
    onClickOutside () {
      this.showResultsDropdown = false
    },

    onFocusIn () {
      return false

      this.cursorTextInterval = setInterval(() => {
        if (this.$refs.cityInput && this.$refs.cityInput.placeholder) {
          this.$refs.cityInput.placeholder = ''
        } else {
          this.$refs.cityInput.placeholder = '|'
        }
      }, 350)
    },

    onFocusOut () {
      return false

      clearInterval(this.cursorTextInterval)

      if (!this.$refs.cityInput) {
        return false
      }

      this.$refs.cityInput.placeholder = 'Where are you starting from?'
    },

    removeCity () {
      this.city = {
        full_name: ''
      }

      this.$emit('input', false)
    },

    onKeyUp () {
      if (!this.searchResults.length || this.index < 1) {
        return false
      }

      this.index--

      if (this.$refs.resultsList) {
        this.$refs.resultsList.scrollTop -= 32
      }

      this.city = {
        ...this.searchResults[this.index]
      }
    },

    onKeyDown () {
      if (!this.searchResults.length) {
        return false
      }

      if (this.index === false) {
        this.index = -1
      }

      this.index++

      if (this.index > 1) {
        if (this.$refs.resultsList) {
          this.$refs.resultsList.scrollTop += 32
        }
      }

      if (this.index > this.searchResults.length - 1) {
        this.index = this.searchResults.length - 1
      }

      this.city = {
        ...this.searchResults[this.index]
      }
    },

    async searchCity (searchValue, set = false) {
      const search = debounce(() => {
        if (this.searchInput.toString() === searchValue.toString()) {
          return false
        }

        this.searchInput = searchValue

        if (!searchValue || searchValue === '') {
          return false
        }

        axios
          .get(`${this.$const('API_URL')}/cities/search`, {
            params: {
              query: searchValue
            }
          })
          .then(r => {
            if (r.data.success) {
              this.searchResults = r.data.entities
              this.index = false

              if (set && this.searchResults.length) {
                this.selectPlace(0)
              }

              if (this.$refs.resultsList) {
                this.$refs.resultsList.scrollTop = 0
              }
            }
          })

        this.showResultsDropdown = true
      }, 500)

      search()
    },

    selectPlace (index, event = false) {
      if (event) {
        event.stopPropagation()
      }

      this.city = {
        ...this.searchResults[index]
      }

      // this.searchResults = []
      this.showResultsDropdown = false

      this.$emit('input', this.city.id)
    }
  }
}
</script>
