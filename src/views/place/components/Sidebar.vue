<template>
  <div class="place-sidebar">
    <div
      v-if="!filteredItems.length && !loading"
    >
      {{ __('No activities') }}
    </div>

    <activity-card
      v-for="item in filteredItems"
      :key="item.id"
      :entity="item"
      @selected="$emit('selected', item)"
      @duration="onDuration($event)"
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
import CardLoading from '../../../components/loading/CardLoading'
import Observer from '../../explore/components/Observer'
import { duration } from '../../../mixins/duration'

export default {
  components: {
    ActivityCard,
    Observer,
    CardLoading
  },

  mixins: [
    duration
  ],

  props: {
    items: {
      type: Array,
      default: () => []
    },

    filters: {
      type: Object,
      required: true
    },

    loading: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      durationMap: {
        //
      }
    }
  },

  computed: {
    filteredItems () {
      if (!this.filters.duration) {
        return this.items
      }

      const min = parseInt(this.filters.duration.min)
      const max = parseInt(this.filters.duration.max)

      return this.items.filter(e => {
        const durationInHours = this.durationInMinutes(this.durationMap[e.id]) / 60

        return durationInHours >= min && durationInHours <= max
      })
    }
  },

  methods: {
    onDuration (item) {
      this.durationMap = {
        ...this.durationMap,
        [item.id]: item.duration
      }
    }
  }
}
</script>
