<template>
  <div
    :style="extraStyle"
    class="cover-image"
  >
    <a
      v-if="previousCity"
      href="#"
      class="text-left previous"
      @click.prevent="goToCity(previousCity.id)"
    >
      <div class="city">
        {{ previousCity.name }}
      </div>

      <div class="country">
        {{ previousCity.name_suffix }}
      </div>

      <div class="sign">
        <i class="fa fa-circle" />
        <span class="line" />
      </div>
    </a>

    <div class="text-center">
      <div class="city">
        {{ currentCity.name }}
      </div>
      <div class="country">
        {{ currentCity.name_suffix }}
      </div>
    </div>

    <a
      v-if="nextCity"
      href="#"
      class="text-left next"
      @click.prevent="goToCity(nextCity.id)"
    >
      <div class="city">
        {{ nextCity.name }}
      </div>
      <div class="country">
        {{ nextCity.name_suffix }}
      </div>

      <div class="sign">
        <span class="line" />
        <i class="fa fa-circle" />
      </div>
    </a>
  </div>
</template>

<script>
import { route } from '../../../mixins/route'

export default {
  mixins: [
    route
  ],

  props: {
    previousCity: {
      type: Object,
      default: () => null
    },

    currentCity: {
      type: Object,
      default: () => null
    },

    nextCity: {
      type: Object,
      default: () => null
    }
  },

  computed: {
    extraStyle () {
      return {
        backgroundImage: 'url(' + this.cityImage + ')'
      }
    },

    cityImage () {
      if (!this.currentCity.image_url) {
        return ''
      }

      return `/storage/${this.currentCity.image_url}`
    }
  },

  methods: {
    goToCity (id) {
      this.$redirect('explore', this.routeParams, `/city/${id}`)
    }
  }
}
</script>
