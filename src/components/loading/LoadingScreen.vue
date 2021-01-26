<template>
  <transition name="fade">
    <div
      v-if="loading"
      class="screen-loading"
    >
      <img src="/travel-loading.gif" alt="Loading..">

      <p class="title">
        {{ texts[index].title }}
      </p>

      <p class="content">
        {{ texts[index].content }}
      </p>
    </div>
  </transition>
</template>

<script>

export default {
  data () {
    return {
      loading: false,
      index: 0,
      texts: [
        {
          title: 'Want to travel with family and friends?',
          content: 'Here we make things more than possible!'
        },
        {
          title: 'What does a vacation sound like?',
          content: 'If I were you, I wouldn\'t be thinking..'
        },
        {
          title: 'Life is too short for wasting time.',
          content: 'The world is waiting to be discovered..'
        }
      ]
    }
  },

  beforeDestroy () {
    this.$eventHub.on('screen-loading', this.triggerLoading)
  },

  mounted () {
    this.$eventHub.on('screen-loading', this.triggerLoading)
  },

  methods: {
    triggerLoading (state) {
      this.index = parseInt(Math.random() * 3)

      this.$emit('loading', state)

      this.loading = state

      if (!state) {
        clearInterval(this.interval)

        this.interval = false
      } else {
        this.interval = setInterval(() => {
          this.index++

          if (this.index === this.texts.length) {
            this.index = 0
          }
        }, 2000)
      }
    }
  }
}
</script>
