<template>
  <nav class="plan-filters">
    <div
      class="nav nav-pills"
    >
      <a
        :class="{ active: tab === 'all' }"
        class="nav-item nav-link cursor-pointer"
        @click.prevent="$emit('switch', 'all')"
      >
        {{ __('All') }}
      </a>

      <a
        :class="{ active: tab === 'individual' }"
        class="nav-item nav-link cursor-pointer"
        @click.prevent="$emit('switch', 'individual')"
      >
        <i class="fa fa-users" />
      </a>
    </div>
  </nav>
</template>

<script>
export default {
  props: {
    tab: {
      type: String,
      required: true
    }
  }
}
</script>
