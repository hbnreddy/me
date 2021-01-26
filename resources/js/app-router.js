const AppRouter = {
  install (Vue) {
    Vue.prototype.$redirect = (url, params, hash, locale = false) => {
      let route = ''

      if (!locale) {
        if (window._locale) {
          locale = window._locale
        } else {
          locale = `/en`
        }
      }

      if (url) {
        route += `/${locale}/${url}`
      } else {
        const path = window.location.pathname[0] === '/'
          ? window.location.pathname.split('/')
            .slice(2)
            .join('/')
          : window.location.pathname.split('/')
            .slice(1)
            .join('/')

        route = `/${locale}/${path}`
      }

      if (Object.keys(params).length) {
        let encode = ''

        Object.keys(params).forEach(key => {
          if (encode !== '') {
            encode += '&'
          }

          encode += `${key}=${params[key]}`
        })

        route += `?${encode}`
      }

      if (hash && hash !== '/') {
        route += `#${hash}`
      }

      window.location.href = route
    }
  }
}

export default AppRouter
