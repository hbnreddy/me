<template>
  <div class="persons">
    <div
      v-for="(traveller, index) in travellers"
      :key="index"
      class="person"
    >
      {{ traveller.reference_key }}
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

  methods: {
    travellerText (traveller) {
      const attributes = traveller.attributes

      if (attributes && attributes.first_name && attributes.last_name) {
        return attributes.first_name[0] + attributes.last_name[0]
      }

      return traveller.key
    }
  }
}
</script>
