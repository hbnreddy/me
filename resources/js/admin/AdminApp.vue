<template>
  <div>
    <div class="d-flex">
      <sidebar />

      <div class="main-content">
        <loading
          :active.sync="loading"
          :is-full-page="true"
        />

        <navbar />

        <router-view />
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import Sidebar from './components/Sidebar'
import Navbar from './components/Navbar'
import Loading from 'vue-loading-overlay'

export default {
  components: {
    Sidebar,
    Navbar,
    Loading
  },

  props: {
    user: {
      type: Object,
      required: true
    }
  },

  watch: {
    $route () {
      `window.scrollTo({
        top: 0,
        behavior: 'smooth'
      })`
    }
  },

  computed: {
    ...mapGetters([
      'loading'
    ])
  },

  created () {
    this.$store.commit('setUser', this.user)
  }
}
</script>
