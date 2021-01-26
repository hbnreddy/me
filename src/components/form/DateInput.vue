<template>
  <masked-input
    :id="id"
    :value="value"
    mask="11/11/1111"
    placeholder="DD / MM / YYYY"
    type="text"
    class="form-control form-control-sm small-input"
    required
    @input="$emit('input', $event)"
  />
</template>

<script>
import MaskedInput from 'vue-masked-input'

export default {
  components: {
    MaskedInput
  },

  props: {
    value: {
      type: String | null,
      required: true
    },

    id: {
      type: String,
      required: true
    }
  }
}
</script>
