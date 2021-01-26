<template>
  <div class="tabs-wrap">
    <ul
      id="myPlans"
      class="nav nav-tabs"
      role="tablist"
    >
      <li
        v-for="(plan, planIndex) in plans"
        :key="planIndex"
        class="nav-item"
      >
        <a
          :id="`plan-${plan.id}-tab`"
          :href="`#plan-${plan.id}`"
          :aria-controls="`plan-${plan.id}`"
          class="nav-link plan-link active"
          data-toggle="tab"
          role="tab"
          aria-selected="false"
          @dblclick.prevent="onDoubleClickPlan(plan.id)"
          @keydown.enter.prevent="renamePlan(plan.id)"
        >
          <span v-if="!renameState[plan.id]">{{ plan.name }}</span>

          <input
            v-if="renameState[plan.id]"
            v-model="renameText"
            class="rename-input"
            type="text"
          >
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    plans: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      renameText: '',
      renameState: {
        //
      }
    }
  },

  methods: {
    onDoubleClickPlan (planId) {
      if (!Object.keys(this.renameState).includes(planId)) {
        this.renameState = {
          ...this.renameState,
          [planId]: true
        }
      } else {
        this.renameState = {
          ...this.renameState,
          [planId]: !this.renameState[planId]
        }
      }

      if (this.renameState[planId]) {
        this.renameText = this.plans.find(e => {
          return e.id === planId
        }).name
      }
    },

    async renamePlan (planId) {
      this.renameState[planId] = false

      await axios
        .post(`/api/cart/plans/${planId}/rename`, {
          name: this.renameText
        })
        .then(r => {
          if (r.data.success) {
            const index = this.plans.findIndex(e => {
              return e.id === planId
            })

            this.$emit('rename', {
              index,
              name: this.renameText
            })
          }
        })
    }
  }
}
</script>
