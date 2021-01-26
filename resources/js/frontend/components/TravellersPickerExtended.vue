<template>
  <div class="dropdown-menu travellers-extended show mt-1">
    <div
      v-for="(traveller, index) in tempTravellers"
      :key="index"
      class="px-3 mb-2"
    >
      <div
        class="form-group custom-control custom-checkbox mb-0"
      >
        <input
          :id="'t-' + traveller.id"
          :checked="traveller.active"
          type="checkbox"
          class="custom-control-input"
          @click="toggleActive(traveller)"
        >

        <label
          :for="'t-' + traveller.id"
          class="custom-control-label"
        >
          {{ traveller.reference_key }}
        </label>
      </div>
    </div>

    <div class="buttons text-center">
      <button
        class="btn btn-orange"
        @click="apply()"
      >
        {{ __('Apply') }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    travellers: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      tempTravellers: []
    }
  },

  watch: {
    travellers: {
      handler (values) {
        this.tempTravellers = [
          ...values
        ]
      },
      immediate: true
    }
  },

  methods: {
    toggleActive (traveller) {
      traveller.active = !traveller.active
    },

    apply () {
      this.$emit('change', [...this.tempTravellers])

      this.emitHide()
    },

    emitHide () {
      this.$emit('hide')
    }
  }
}
</script>
