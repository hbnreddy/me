<template>
  <div class="themes-block">
    <div class="themes">
      <nav class="nav list">
        <a
          v-for="(theme, index) in localThemes"
          :key="index"
          :class="{'active': theme.id === currentTheme}"
          class="nav-link theme"
          @click="setCurrentTheme(theme.id)"
        >
          {{ theme.name }}
        </a>
      </nav>

      <toggle-input
        :enabled="addedToCart"
        :show-text="true"
        :left-text="'Available'"
        :right-text="'Added'"
        @input="addedToCart = !!$event"
      />
    </div>

    <theme-row
      v-for="theme in localThemes"
      :ref="theme.id"
      :key="theme.id"
      :added-to-cart="addedToCart"
      :theme="theme"
      :city-id="cityId"
      @loaded="onThemeRowLoaded($event)"
    />
  </div>
</template>

<script>
import ToggleInput from '../../components/ToggleInput'
import ThemeRow from './ThemeRow'

export default {
  components: {
    ToggleInput,
    ThemeRow
  },

  props: {
    cityId: {
      type: Number,
      required: true
    },

    themes: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      localThemes: [],
      currentTheme: false,
      addedToCart: false
    }
  },

  watch: {
    themes: {
      handler () {
        this.localThemes = [
          ...this.themes.map(e => {
            return {
              ...e,
              loaded: false
            }
          })
        ]

        if (this.localThemes.length) {
          this.currentTheme = this.localThemes[0].id
        }
      },
      immediate: true
    }
  },

  methods: {
    onThemeRowLoaded (id) {
      const index = this.themes.findIndex(e => {
        return e.id === id
      })

      if (index < 0) {
        return false
      }

      this.localThemes.splice(index, 1, {
        ...this.localThemes[index],
        loaded: true
      })
    },

    setCurrentTheme (id) {
      let element = this.$refs[id]
      let top = element[0].$el.offsetTop

      window.scrollTo({
        top,
        behavior: 'smooth'
      })

      this.currentTheme = id
    }
  }
}
</script>
