<template>
  <masked-input
    :id="id"
    v-model="value"
    mask="11 / 11 / 1111"
    placeholder-char=" "
    placeholder="DD / MM / YYYY"
    type="text"
    class="form-control small-input"
    required
    @input="onInput()"
  />
</template>

<script>
import MaskedInput from 'vue-masked-input'

export default {
  components: {
    MaskedInput
  },

  props: {
    id: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      value: ''
    }
  },

  methods: {
    onInput () {
      this.$emit('input', this.value)
    }
  }
}
</script>
