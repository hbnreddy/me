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

      <div class="mb-4 text-left">
        {{ __('You have no plans for today') }}. {{ __('Try Exploring another city') }}
      </div>

      <div class="text-center">
        <button
          class="btn btn-explore"
          @click.prevent="goToExplore()"
        >
          {{ __('Explore') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>

import LoadingCircle from '../../../../components/LoadingCircle'
import { route } from '../../../mixins/route'

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

  methods: {
    goToExplore () {
      this.$redirect('explore', this.routeParams)
    }
  }
}
</script>
