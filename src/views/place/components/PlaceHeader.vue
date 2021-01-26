<template>
  <div class="poi-header">
    <div class="title mb-1">
      {{ entity.name }}
      <span class="review ml-3">
        <i class="fa fa-star text-primary" />
        <span class="font-weight-bold">&nbsp;{{ entity.rating }}</span>

        <span v-if="entity.reviews_number">({{ entity.reviews_number }})</span>
      </span>
    </div>

    <div
      v-if="entity.price"
      class="price mb-1"
    >
      <div class="amount">
        {{ entity.price.currency }}
        {{ Math.round(parseFloat(entity.price.value)) }}
      </div>
      <div class="small">
        {{ __('per person') }}
      </div>
    </div>

    <div class="button mb-1">
      <button
        v-if="!itemInCart"
        class="btn btn-orange blob orange"
        data-toggle="modal"
        data-target="#addPlanner"
        style="display: flex; align-items: center;"
        @click.prevent="openPlanModal()"
      >
        <loading-circle
          :loading="loading"
          :small="true"
          class="mr-3"
        />

        {{ __('Add to Plan') }}
      </button>

      <button
        v-else
        class="btn btn-orange"
        data-toggle="modal"
        data-target="#addPlanner"
        style="display: flex; align-items: center;"
        @click.prevent="remove()"
      >
        <loading-circle
          :loading="loading"
          :small="true"
          class="mr-3"
        />

        {{ __('Remove from planner') }}
      </button>
    </div>

    <add-to-plan-modal
      v-if="planModal"
      :item="entity"
      @added="returnToCity()"
      @hide="planModal = false"
    />
  </div>
</template>

<script>
import AddToPlanModal from '../../../components/modals/planning/AddToPlanModal'
import LoadingCircle from '../../../components/loading/LoadingCircle'

import { mapGetters } from 'vuex'
import { route } from '../../../mixins/route'

export default {
  components: {
    LoadingCircle,
    AddToPlanModal
  },

  mixins: [
    route
  ],

  props: {
    entity: {
      type: Object,
      required: true
    },

    venues: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      inCart: false,
      loading: false,
      planModal: false
    }
  },

  computed: {
    ...mapGetters([
      'notifications',
      'currentPlanId',
      'currentPlan'
    ]),

    itemInCart () {
      return this.currentPlan ? this.currentPlan.items.findIndex(e => {
        return e.id.toString() === this.entity.id.toString()
      }) >= 0
        : false
    }
  },

  methods: {
    async openPlanModal () {
      this.planModal = true
    },

    returnToCity () {
      this.planModal = false

      this.$router.push({
        name: 'city',
        params: {
          id: this.$route.params.cityId,
          index: this.$route.params.cityIndex,
          planId: this.currentPlanId
        },
        query: this.routeQuery
      })
    },

    async remove () {
      this.loading = true

      this.$store.dispatch('removeItem', this.entity.id)

      this.loading = false
    }
  }
}
</script>
