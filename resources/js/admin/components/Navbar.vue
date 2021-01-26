<template>
  <div class="navbar-container">
    <div class="d-flex justify-content-end align-items-center">
      <a
        href="#"
        class="user"
        style="position: relative;"
        @click.prevent="dropdown = true"
      >
        <i class="fa fa-user mr-2" />
        {{ user.first_name }} {{ user.last_name }}

        <i class="fa fa-angle-down" />

        <div
          v-if="dropdown"
          v-on-clickaway="hideDropdown"
          class="dropdown-results"
          style="position: absolute; min-width: 120px; max-width: 120px; right: 0; top: 24px;"
        >
          <ul class="results-list">
            <li class="results-list-item">
              <a href="#" @click.prevent="logout()">
                Logout
              </a>
            </li>
          </ul>
        </div>
      </a>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  data () {
    return {
      dropdown: false
    }
  },

  computed: {
    ...mapGetters([
      'user'
    ])
  },

  methods: {
    logout () {
      window.location.href = '/logout'
    },

    hideDropdown () {
      this.dropdown = false
    }
  }
}
</script>
