<template>
  <div style="display: none;" />
</template>

<script>
import AppRouter from './app-router'

export default {
  beforeMount () {
    AppRouter.eventBus.$on('redirect', this.redirect)
  },

  methods: {
    redirect (url, params) {
      let route = url

      if (Object.keys(params).length) {
        let encode = ''

        Object.keys(params).forEach(key => {
          if (encode !== '') {
            encode += '&'
          }

          encode += `${key}=${params[key]}`
        })

        route = `?${encode}`
      }

      return route
    }
  }
}
</script>
