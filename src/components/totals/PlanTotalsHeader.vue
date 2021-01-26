<template>
  <div class="tabs-wrap">
    <ul
      id="myPlans"
      class="nav nav-tabs"
      role="tablist"
    >
      <li class="nav-item">
        <a
          :id="`plan-${plan.id}-tab`"
          :aria-controls="`plan-${plan.id}`"
          class="nav-link plan-link active"
          data-toggle="tab"
          role="tab"
          aria-selected="false"
          @dblclick.prevent="onDoubleClickPlan(plan.id)"
          @keydown.enter.prevent="renamePlan(plan.id)"
        >
          <span v-if="!renameState[plan.id]">
            Edit plan name
          </span>

          <input
            v-if="renameState[plan.id]"
            v-model="renameText"
            class="rename-input"
            type="text"
          >
        </a>
      </li>
    </ul>

    <section class="actions">
      <a class="btn-link">
        <i class="fa fa-share" />
      </a>

      <a class="btn-link">
        <i class="fa fa-download" />
      </a>
    </section>
  </div>
</template>

<script>

export default {
  props: {
    plan: {
      type: Object,
      required: true
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
        this.renameText = this.plan.name
      }
    },

    renamePlan (planId) {
      this.$emit('rename-plan', planId, this.renameText)
    }
  }
}
</script>
