<template>
  <content-loader
    :speed="1"
    width="280"
    height="300"
    :animate="true"
  >
    <rect
      x="0" y="10"
      rx="3" ry="3" width="410" height="6"
    />
    <rect
      x="30" y="32"
      rx="3" ry="3" width="350" height="6"
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
