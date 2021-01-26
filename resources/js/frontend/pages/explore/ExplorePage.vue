<template>
  <div class="application">
    <loading-circle
      :loading="loading"
      :small="false"
      :color="'#FD753B'"
    />

    <navbar />

    <router-view @loading="onLoading($event)" />

    <footer-component />
  </div>
</template>

<script>
import Navbar from '../../components/Navbar'
import FooterComponent from '../../components/Footer'

export default {
  components: {
    Navbar,
    FooterComponent
  },

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      loading: false
    }
  },

  created () {
    if (this.data.user) {
      this.$store.commit('setAuthUser', this.data.user)
    }

    this.$store.commit('setCities', this.data.cities)
    this.$store.commit('setNotifications', this.data.notifications)
    this.$store.commit('setCurrentPlanId', this.data.current_plan_id)
    this.$store.commit('setThemes', this.data.themes)
    this.$store.commit('setQuery', this.data.query)
  },

  methods: {
    onLoading (state) {
      this.loading = state
    }
  }
}
</script>
