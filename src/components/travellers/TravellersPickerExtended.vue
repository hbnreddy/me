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
          {{ printName(traveller) }}
        </label>
      </div>
    </div>

    <div class="buttons text-right">
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
import { cloneDeep } from 'lodash'
import {mapGetters} from 'vuex'

export default {
  props: {
    travellers: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      referenceKeyMap: {
        'A': 'Adult',
        'C': 'Child',
        'I': 'Infant'
      },
      tempTravellers: []
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ])
  },

  watch: {
    travellers: {
      handler () {
        this.tempTravellers = cloneDeep(this.travellers)
      },
      immediate: true
    }
  },

  methods: {
    toggleActive (traveller) {
      traveller.active = !traveller.active
    },

    apply () {
      this.$emit('change', cloneDeep(this.tempTravellers))

      this.emitHide()
    },

    emitHide () {
      this.$emit('hide')
    },

    referenceKeyString (key) {
      const type = key[0]

      return `${this.referenceKeyMap[type]} ${key[1]}`
    },

    printName (traveller) {
      const index = this.currentPlan.travellers.findIndex(e => {
        return e.reference_key === traveller.reference_key
      })
      const attributes = this.currentPlan.travellers[index]

      let fullName = traveller.reference_key

      if (attributes.first_name) {
        fullName = attributes.first_name

        if (attributes.last_name) {
          fullName = `${fullName} ${attributes.last_name}`
        }
      }

      return fullName
    }
  }
}
</script>
