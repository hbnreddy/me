<template>
  <div>
    <div class="stories">
      <h6 class="font-weight-bold mb-3">
        {{ __('Travellers stories') }}
      </h6>
    </div>

    <div class="d-flex align-items-center mb-4">
      <story-line
        :stories="mediaResources"
        :active="activeStory"
        :max-stories="14"
        style="flex: 1;"
        @change="onChangeStory($event)"
      />

      <story-wall
        :stories="mediaResources"
        :active="activeStory"
        :max-stories="14"
        style="flex: 3;"
        @change="onChangeStory($event)"
      />
    </div>
  </div>
</template>

<script>
import StoryLine from './components/StoryLine'
import StoryWall from './components/StoryWall'

export default {
  components: {
    StoryLine,
    StoryWall
  },

  props: {
    mediaResources: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      activeStory: 0
    }
  },

  methods: {
    onChangeStory (storyIndex) {
      this.activeStory = storyIndex
    }
  }
}
</script>
