<template>
  <div class="to-do">
    <h6 class="font-weight-bold mb-3 text-capitalize">
      {{ itemsCountString(items.length) }}
    </h6>

    <div class="carousel">
      <carousel
        :per-page-custom="[[320, 1], [526, 2], [1025, 4]]"
        :mouse-drag="true"
        :autoplay="false"
        :autoplay-timeout="5000"
        :pagination-enabled="false"
        :navigation-enabled="true"
        :navigation-next-label="'<i class=\'fa fa-angle-right slide-next\'></i>'"
        :navigation-prev-label="'<i class=\'fa fa-angle-left slide-prev\'></i>'"
      >
        <slide
          v-for="i in 4"
          v-if="!items.length"
          :key="i"
        >
          <card-loading />
        </slide>

        <slide
          v-for="item in items"
          :key="item.id"
        >
          <div class="card">
            <a
              class="text-decoration-none"
            >
              <div
                class="card-img"
              >
                <img
                  v-onload="item.image"
                  class="card-img-top activity-image"
                  style="height: 125px;"
                  alt="..."
                >

                <span class="indoors">
                  <i class="fa fa-sign-in" />
                </span>
              </div>
            </a>

            <div class="card-body">
              <div class="flex-body">
                <div>
                  <a
                    href="#"
                    class="card-title"
                  >
                    {{ activityTextLines(item.name, 2) }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </slide>
      </carousel>
    </div>
  </div>
</template>

<script>
import CardLoading from '../../../components/loading/CardLoading'

export default {
  components: {
    CardLoading
  },

  props: {
    items: {
      type: Array,
      default: () => []
    }
  },

  data () {
    return {
      visible: false,
      fullDescription: {
        //
      }
    }
  },

  mounted () {
    setTimeout(() => {
      this.visible = true
    }, 1000)
  },

  methods: {
    itemsCountString (number) {
      switch (number) {
      case 0:
        return `${this.__('No')} ${this.__('things to do')}`
      case 1:
        return `${this.__('One')} ${this.__('thing to do')}`
      case 2:
        return `${this.__('Two')} ${this.__('things to do')}`
      case 3:
        return `${this.__('Three')} ${this.__('things to do')}`
      case 4:
        return `${this.__('Four')} ${this.__('things to do')}`
      case 5:
        return `${this.__('Five')} ${this.__('things to do')}`
      default:
        return ''
      }
    },

    showFullDescription (id, state) {
      this.fullDescription = {
        ...this.fullDescription,
        [id]: state
      }
    },

    shortText (text) {
      if (!text) {
        return ''
      }

      return text.substr(0, 36) + '..'
    },

    activityTextLines (text, linesCount = 1) {
      if (!text) {
        return ''
      }

      const charsPerLine = 40
      const maxChars = charsPerLine * linesCount

      if (text.length > maxChars) {
        return text.substr(0, charsPerLine * linesCount) + '...'
      }

      return text
    }
  }
}
</script>
