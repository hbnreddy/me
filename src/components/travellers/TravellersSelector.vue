<template>
  <div class="dropdown-menu travellers activities-travellers show mt-1">
    <div
      v-for="(traveller, index) in tempTravellers"
      :key="index"
      class="dropdown-item"
    >
      <div
        class="custom-control custom-checkbox"
      >
        <i class="fa fa-user" />

        <input
          :id="`checkbox-${traveller.type + index}`"
          v-model="tempTravellers[index].active"
          type="checkbox"
          class="custom-control-input"
        >

        <label
          :for="`checkbox-${traveller.type + index}`"
          class="custom-control-label text-capitalize cursor-pointer"
        >
          {{ typesTextMapping[traveller.type] }} {{ traveller.reference_key }}
        </label>
      </div>
    </div>

    <div class="buttons text-right">
      <a
        href="#"
        class="mx-2 text-dark"
        @click.prevent="onCancel()"
      >{{ __('Cancel') }}</a>

      <a
        href="#"
        class="mx-2 text-primary"
        @click.prevent="onApply()"
      >{{ __('Apply') }}</a>
    </div>
  </div>
</template>

<script>
import { cloneDeep } from 'lodash'

export default {
  props: {
    values: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      tempTravellers: [],
      typesTextMapping: {
        adults: 'adult',
        children: 'child',
        infants: 'infant'
      }
    }
  },

  created () {
    this.tempTravellers = cloneDeep(this.values)
  },

  methods: {
    onApply () {
      this.$emit('input', [
        ...this.tempTravellers
      ])

      this.hide()
    },

    onCancel () {
      this.hide()
    },

    hide () {
      this.$emit('hide')
    }
  }
}
</script>
