<template>
  <div class="theme-picker">
    <ul class="months-list">
      <li
        v-for="theme in themes"
        :key="theme.id"
        :class="{ 'selected': isSelected(theme.id) }"
        class="list-item"
        @click.prevent="toggleSelect(theme.id)"
      >
        <a>{{ theme.name }}</a>
      </li>
    </ul>

    <div class="d-flex justify-content-end align-items-center bottom-buttons">
      <a
        href="#"
        class="text-decoration-none"
        style="margin-right: 20px;color: #333;"
        @click.prevent="onCancel()"
      >{{ __('Cancel') }}
      </a>

      <a
        href="#"
        class="text-decoration-none"
        @click.prevent="onApply()"
      >{{ __('Apply') }}
      </a>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    themes: {
      type: Array,
      default: () => []
    },

    selected: {
      type: Array | Boolean,
      default: () => []
    }
  },

  data () {
    return {
      selectedThemes: []
    }
  },

  created () {
    if (this.selected && this.selected.length) {
      this.selectedThemes = [...this.selected]
    }
  },

  methods: {
    onApply () {
      this.$emit('input', [...this.selectedThemes])

      this.emitHide()
    },

    onCancel () {
      this.emitHide()
    },

    toggleSelect (id) {
      if (this.selectedThemes.includes(id)) {
        const index = this.selectedThemes.indexOf(id)
        this.selectedThemes.splice(index, 1)
      } else {
        this.selectedThemes.push(id)
      }
    },

    isSelected (id) {
      return this.selectedThemes.includes(parseInt(id))
    },

    emitHide () {
      this.$emit('hide')
    }
  }
}
</script>
