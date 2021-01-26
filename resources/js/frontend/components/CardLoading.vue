<template>
  <content-loader
    :speed="1"
    :width="280"
    :height="300"
    :animate="true"
    style="border: 1px solid #ddd; margin-right: 10px; border-radius: 6px;"
  >
    <circle
      cx="333.40099117860757"
      cy="371.67099117860755"
      r="45.40099117860754"
    />
    <circle
      cx="132.26035793053342"
      cy="377.5303579305334"
      r="45.260357930533424"
    />
    <circle
      cx="289.53897688809354"
      cy="305.8089768880935"
      r="54.53897688809353"
    />
    <rect
      x="80.04"
      y="312.09"
      rx="0"
      ry="0"
      width="324"
      height="66"
      transform="rotate(0.06, 80.04, 312.09)"
    />
    <circle
      cx="163.7954915950234"
      cy="303.06549159502345"
      r="48.79549159502341"
    />
    <rect
      x="159.7"
      y="255.09"
      rx="0"
      ry="0"
      width="131"
      height="64"
      transform="rotate(357.56, 159.7, 255.09)"
    />
    <rect
      x="20"
      y="40"
      rx="3"
      ry="3"
      width="170"
      height="10"
    />
    <rect
      x="0"
      y="60"
      rx="3"
      ry="3"
      width="250"
      height="10"
    />
    <rect
      x="20"
      y="80"
      rx="3"
      ry="3"
      width="200"
      height="10"
    />
    <rect
      x="20"
      y="100"
      rx="3"
      ry="3"
      width="80"
      height="10"
    />
  </content-loader>
</template>

<script>
import {ContentLoader} from 'vue-content-loader'

export default {
  components: {
    ContentLoader
  },
  props: {
    data: {
      type: Object,
      default: () => {
      }
    }
  }
}
</script>
