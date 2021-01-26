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
        v-if="!inCart"
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
        class="btn btn-orange blob orange"
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
import AddToPlanModal from '../../../modals/AddToPlanModal'
import LoadingCircle from '../../../../components/LoadingCircle'

import { mapGetters } from 'vuex'
import axios from 'axios'
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
      'currentPlanId',
      'notifications'
    ])
  },

  created () {
    this.checkAdded()
  },

  methods: {
    async openPlanModal () {
      this.planModal = true
    },

    async checkAdded () {
      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/items/${this.entity.id}/exists`)
        .then(r => {
          this.inCart = r.data.state
        })
    },

    returnToCity () {
      this.$redirect('explore', this.routeParams, `/city/${this.entity.identifiers.city_id}`)
    },

    async remove () {
      this.loading = true

      await axios
        .post(`/api/cart/plans/${this.currentPlanId}/items/${this.entity.id}/remove`, {
          type: this.entity.type
        })
        .then(r => {
          if (r.data.success) {
            this.inCart = false
            this.$store.commit('setNotifications', {
              count: this.notifications.count - 1
            })
          }
        })

      this.loading = false
    }
  }
}
</script>
