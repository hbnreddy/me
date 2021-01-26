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

    <div class="checkout-btn-block d-flex justify-content-center align-items-center">
      <a
        :class="{ 'blob orange': formsComplete }"
        class="btn-checkout"
        @click.prevent="$emit('store')"
      >
        {{ __('Update Details') }}
      </a>
    </div>
  </div>
</template>

<script>
import {print} from '../../mixins/print'

export default {
  mixins: [
    print
  ],

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

  computed: {
    formsComplete () {
      return Object.values(this.completed)
        .filter(e => !!e).length === this.travellers.length
    }
  },

  methods: {
    isCompleted (id) {
      return this.completed[id]
    },

    onChange (index) {
      this.$emit('change', index)
    }
  }
}
</script>
