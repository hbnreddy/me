<template>
  <transition name="fade">
    <div
      v-if="visible"
      v-on-clickaway="hide"
      class="notification"
    >
      <div class="wrapper">
        <p
          :class="notifType"
          class="title"
        >
          <i v-if="notifType === 'error'" class="fa fa-times-circle" />
          <i v-if="notifType === 'warning'" class="fa fa-exclamation-circle" />
          <i v-if="notifType === 'success'" class="fa fa-check-circle" />

          {{ __(title) }}
        </p>

        <p class="description">
          {{ __(message) }}
        </p>
      </div>
    </div>
  </transition>
</template>

<script>
import Notification from './notification'

import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    text: {
      type: String | Boolean,
      default: false
    },

    type: {
      type: String | Boolean,
      default: false
    }
  },

  data () {
    return {
      title: false,
      message: '',
      visible: false,
      notifType: 'error'
    }
  },

  mounted () {
    if (this.text) {
      this.show({
        text: this.text,
        type: this.notifType
      })
    }
  },

  beforeMount () {
    Notification.eventBus.$on('show', this.show)
  },

  methods: {
    show ({ title, text, type }) {
      this.visible = true

      this.title = title
      if (!title) {
        this.title = type
      }

      this.notifType = type
      this.message = text

      setTimeout(() => {
        this.visible = false
      }, 4000)
    },

    hide () {
      this.visible = false
    }
  }
}
</script>
