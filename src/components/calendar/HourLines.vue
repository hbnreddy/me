<template>
  <div class="hour-lines">
    <div
      v-for="i in hours"
      :key="i"
      class="vuecal__time-cell"
      style="height: 90px;"
    >
      <span class="line" />

      <span class="label">{{ i }}</span>
    </div>
  </div>
</template>

<script>

export default {
  props: {
    hours: {
      type: Array,
      default: () => []
    }
  }
}
</script>
