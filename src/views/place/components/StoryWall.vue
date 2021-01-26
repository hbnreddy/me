<template>
  <div class="story-wall">
    <div
      v-for="(circle, index) in computedCircles"
      :key="index"
      :class="{ active: index === active }"
      :style="{
        width: circle.width + 'px',
        height: circle.height + 'px',
        left: circle.x + 'px',
        top: circle.y + 'px',
        backgroundImage: circle.backgroundImage
      }"
      class="story-circle"
    />
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

  data () {
    return {
      minWidth: 40,
      minHeight: 40,
      maxWidth: 80,
      maxHeight: 80
    }
  },

  computed: {
    currentStory () {
      return 'url(' + this.stories[this.active].url + ')'
    },

    computedCircles () {
      let circles = []

      const maxWidth = 400
      const maxHeight = 340

      let currentWidth = 0
      let currentHeight = 0

      this.stories.forEach((story, index) => {
        if (index < this.maxStories) {
          const randomSize = this.randomSize()

          if (currentWidth > maxWidth) {
            currentWidth = 40
            currentHeight += 120
          } else {
            currentWidth += 120
          }

          if (currentHeight < maxHeight + 20) {
            circles.push({
              x: currentWidth,
              y: currentHeight,
              width: randomSize,
              height: randomSize,
              backgroundImage: 'url(' + story.url + ')'
            })
          }
        }
      })

      return circles
    }
  },

  methods: {
    randomSize () {
      return Math.floor(Math.random() * 40) + 50
    },

    randomPosition () {
      return {
        x: Math.floor(Math.random() * 300),
        y: Math.floor(Math.random() * 300)
      }
    }
  }
}
</script>
