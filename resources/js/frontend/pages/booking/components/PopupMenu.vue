<template>
  <ul class="popup-menu">
    <li
      v-for="(item, itemIndex) in items"
      :key="itemIndex"
      class="menu-item"
      @click.prevent="$emit('input', itemIndex)"
    >
      <a href="#">
        {{ item.text }}
      </a>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default: () => []
    }
  }
}
</script>
