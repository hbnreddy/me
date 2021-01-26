<template>
  <div class="no-plans">
    <div
      v-if="loading"
    >
      <loading-circle
        :loading="loading"
      />

      <div class="text-center">
        {{ __('Loading activities') }}
      </div>
    </div>

    <div
      v-else
    >
      <div class="text-center snap">
        <i class="fa fa-meh-o" />
      </div>

      <div class="mb-4 text-center">
        {{ __('You have no plans for today') }}. {{ __('Try Exploring another city') }}
      </div>

      <div class="text-center">
        <router-link
          :to="exploreRoute"
          class="btn btn-explore"
        >
          {{ __('Explore') }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingCircle from '../loading/LoadingCircle'

import { mapGetters } from 'vuex'
import { route } from '../../mixins/route'

export default {
  components: {
    LoadingCircle
  },

  mixins: [
    route
  ],

  props: {
    loading: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ]),

    exploreRoute () {
      return {
        name: 'explore',
        params: {
          planId: this.currentPlanId
        }
      }
    }
  },

  methods: {
    goToExplore () {
      this.$redirect('explore', this.routeParams)
    }
  }
}
</script>
