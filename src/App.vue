<template>
  <div id="app" class="application">
    <boot-application />

    <application-page
      :style="{ 'height: 0': loading }"
    />

    <notification-view />

    <boot-modals />

    <loading-screen @loading="loading = $event" />
  </div>
</template>

<script>
import BootApplication from './bootstrap/BootApplication'
import BootModals from './bootstrap/BootModals'
import ApplicationPage from './ApplicationPage'
import NotificationView from './components/NotificationView'

export default {
  components: {
    BootApplication,
    BootModals,
    NotificationView,
    ApplicationPage
  },

  data () {
    return {
      loading: false
    }
  }
}
</script>