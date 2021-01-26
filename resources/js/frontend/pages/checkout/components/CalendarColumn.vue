<template>
  <div
    column=""
    tabindex="0"
    class="vuecal__flex vuecal__cell-content calendar-column"
  >
    <div
      v-if="loading || !items.length"
      class="vuecal__no-event"
    >
      <no-plans-exception
        :loading="loading"
      />
    </div>

    <div
      v-else
      class="vuecal__cell-events"
    >
      <day-column
        :items="items"
      />
    </div>
  </div>
</template>

<script>
import NoPlansException from './NoPlansException'
import DayColumn from './DayColumn'

import {mapGetters} from 'vuex'
import axios from 'axios'

export default {
  components: {
    NoPlansException,
    DayColumn
  },

  props: {
    day: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      loading: false,
      items: []
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ])
  },

  watch: {
    day: {
      handler () {
        this.items = []

        this.fetchItems()
      },
      immediate: true
    }
  },

  methods: {
    async fetchItems () {
      this.loading = true

      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/items`, {
          params: {
            date: this.day.date
          }
        })
        .then(r => {
          if (r.data.success) {
            this.items = r.data.entities
          }

          this.loading = false
        })
    }
  }
}
</script>
