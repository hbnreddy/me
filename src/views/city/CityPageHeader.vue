<template>
  <div
    :style="extraStyle"
    class="cover-image"
  >
    <router-link
      v-if="previousCity"
      :to="previousCityRoute"
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
    </router-link>

    <div class="text-center">
      <div class="city">
        {{ city.name }}
      </div>
      <div class="country">
        {{ city.name_suffix }}
      </div>
    </div>

    <router-link
      v-if="nextCity"
      :to="nextCityRoute"
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
    </router-link>
  </div>
</template>

<script>
import { route } from '../../mixins/route'
import { mapGetters } from 'vuex'

export default {
  mixins: [
    route
  ],

  props: {
    previousCity: {
      type: Object,
      default: () => null
    },

    city: {
      type: Object,
      default: () => null
    },

    nextCity: {
      type: Object,
      default: () => null
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ]),

    previousCityRoute () {
      return {
        name: 'city',
        params: {
          id: this.previousCity.id,
          index: this.previousCity.index,
          planId: this.currentPlanId
        },
        query: this.routeQuery
      }
    },

    nextCityRoute () {
      return {
        name: 'city',
        params: {
          id: this.nextCity.id,
          index: this.nextCity.index,
          planId: this.currentPlanId
        },
        query: this.routeQuery
      }
    },

    extraStyle () {
      return {
        backgroundImage: 'url(' + '../../../assets/landing-page-image.jpg' + ')'
      }
    },

    cityImage () {
      if (!this.city.image_url) {
        return ''
      }

      return `/${this.city.image_url}`
    }
  },

  methods: {
    goToCity (id) {
      // this.$redirect('explore', this.routeParams, `/city/${id}`)
    }
  }
}
</script>
