<template>
  <div
    v-if="inclusions.length || refundPolicies.length"
    class="place-policy informations"
  >
    <div v-if="inclusions.length">
      <h6 class="font-weight-bold mb-3">
        {{ __('Inclusions') }}
      </h6>

      <ul>
        <li
          v-for="(item, index) in inclusions"
          :key="index"
        >
          {{ item.name }}
        </li>
      </ul>
    </div>

    <div v-if="refundPolicies.length">
      <h6 class="font-weight-bold mb-3">
        {{ __('Cancellation policy') }}
      </h6>

      <ul>
        <li
          v-for="(item, index) in refundPolicies"
          :key="index"
        >
          {{ __('You will receive') }}
          {{ item.value }} {{ item.symbol }}
          {{ __('if you cancel up to') }} {{ item.period }}
          {{ __('before the activity begins') }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    inclusions: {
      type: Array,
      default: () => []
    },

    exclusions: {
      type: Array,
      default: () => []
    },

    refundPolicies: {
      type: Array,
      default: () => []
    }
  },

  computed: {
    isActivity () {
      return this.entity.type === 'activity'
    }
  }
}
</script>
