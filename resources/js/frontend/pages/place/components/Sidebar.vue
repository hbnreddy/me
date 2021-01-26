<template>
  <div class="place-sidebar">
    <div
      v-if="!items.length && !loading"
    >
      {{ __('No activities') }}
    </div>

    <activity-card
      v-for="item in items"
      :key="item.id"
      :entity="item"
      @selected="$emit('selected', item)"
    />

    <observer @intersect="$emit('loadMore')" />

    <card-loading
      v-for="i in 4"
      v-if="loading"
      :key="i"
      class="mb-4 mr-0"
    />
  </div>
</template>

<script>
import ActivityCard from './ActivityCard'
import CardLoading from '../../../components/CardLoading'
import Observer from '../../explore/components/Observer'

export default {
  components: {
    ActivityCard,
    Observer,
    CardLoading
  },

  props: {
    items: {
      type: Array,
      default: () => []
    },

    loading: {
      type: Boolean,
      default: false
    }
  }
}
</script>
