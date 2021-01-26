<template>
  <div class="application">
    <loading-circle
      :loading="loading"
      :small="false"
      :color="'#FD753B'"
    />

    <navbar v-if="$route.name !== 'welcome'" />

    <router-view />

    <app-footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar'
import AppFooter from './components/AppFooter'

export default {
  components: {
    Navbar,
    AppFooter
  },

  data () {
    return {
      loading: false
    }
  },

  methods: {
    onLoading (state) {
      this.loading = state
    }
  }
}
</script>
