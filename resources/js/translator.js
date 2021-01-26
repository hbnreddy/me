const Translator = {
  install (Vue) {
    Vue.prototype.__ = (key, replace) => {
      let translation = true

      try {
        const searchKey = key.toLowerCase().replace(/ /g, '_')

        translation = window._translations[window._locale]['json'][searchKey]
          ? window._translations[window._locale]['json'][searchKey]
          : key
      } catch (e) {
        translation = key
      }

      // _.forEach(replace, (value, key) => {
      //   translation = translation.replace(':' + key, value)
      // })

      return translation
    }
  }
}

export default Translator
