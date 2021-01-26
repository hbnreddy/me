<template>
  <div class="travellers-tabs">
    <ul
      id="travellersTabs"
      role="tablist"
      class="nav nav-tabs"
    >
      <li
        v-for="(traveller, index) in travellers"
        :key="index"
        :index="index"
        class="nav-item"
      >
        <a
          :class="{ active: index === activeIndex, completed: isCompleted(traveller.id) }"
          href="#"
          aria-selected="false"
          class="nav-link plan-link"
          @click.prevent="onChange(index)"
        >
          {{ initials(traveller) }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    travellers: {
      type: Array,
      default: () => []
    },

    activeIndex: {
      type: Number,
      default: 0
    },

    completed: {
      type: Object,
      required: true
    }
  },

  methods: {
    initials (traveller) {
      return (traveller.reference_key || traveller.attributes.nickname).toUpperCase()
    },

    isCompleted (id) {
      return this.completed[id]
    },

    onChange (index) {
      this.$emit('change', index)
    }
  }
}
</script>
