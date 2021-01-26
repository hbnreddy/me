<template>
  <transition name="fade">
    <div
      v-if="visible"
      v-on-clickaway="hide"
      class="notification"
    >
      <div class="wrapper">
        <p
          :class="notificationType"
          class="title"
        >
          <i v-if="notificationType === 'error'" class="fa fa-times-circle" />
          <i v-if="notificationType === 'warning'" class="fa fa-exclamation-circle" />
          <i v-if="notificationType === 'success'" class="fa fa-check-circle" />
          <i v-if="notificationType === 'wait'" class="fa fa-wait" />

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
      notificationType: 'error'
    }
  },

  mounted () {
    if (this.text) {
      this.show({
        text: this.text,
        type: this.notificationType
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

      this.notificationType = type
      this.message = text

      if (type !== 'wait') {
        setTimeout(() => {
          this.visible = false
        }, 3000)
      }
    },

    hide () {
      this.visible = false
    }
  }
}
</script>
