<template>
  <div
    :style="{ backgroundImage: currentStory }"
    class="story-line"
  >
    <div class="lines">
      <div
        v-for="(story, index) in filteredStories"
        :key="index"
        :class="{ active: index === active }"
        class="line"
        @click.prevent="changeStory(index)"
      />
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stories: {
      type: Array,
      default: () => []
    },

    active: {
      type: Number,
      default: 0
    },

    maxStories: {
      type: Number,
      default: 11
    }
  },

  computed: {
    currentStory () {
      if (!this.stories[this.active]) {
        return `url('/')`
      }

      return `url(${this.stories[this.active].url})`
    },

    filteredStories () {
      return this.stories.filter((story, index) => index < this.maxStories)
    }
  },

  mounted () {
    setInterval(() => {
      let active = this.active
      active++

      if (active >= this.maxStories || active >= this.stories.length) {
        active = 0
      }

      this.emitChange(active)
    }, 3000)
  },

  methods: {
    changeStory (index) {
      this.emitChange(index)
    },

    emitChange (value) {
      this.$emit('change', value)
    }
  }
}
</script>
