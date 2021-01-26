<template>
  <div class="image-gallery">
    <vue-gallery
      :images="images"
      :index="galleryIndex"
      @close="galleryIndex = null"
    />

    <div class="card">
      <div
        :class="'grid-images-' + gridLength()"
        class="grid-images"
        @click.prevent="openGallery()"
      >
        <div
          v-for="(image, index) in images"
          v-if="index < 4"
          :key="index"
          :class="'grid-' + (index + 1)"
          :style="{ backgroundImage: 'url(' + image + ')' }"
          class="grid-item"
        >
          <div
            v-if="images.length - 4 > 0"
            class="more-images"
          >
            + {{ images.length - 4 }} {{ __('more') }}
          </div>
        </div>
      </div>

      <div class="card-body">
        <div class="flex-body">
          <div
            class="mr-1"
            style="width: 100%;"
          >
            <a
              href="#"
              class="card-title"
              @click.prevent="$emit('selected')"
            >
              {{ entity.name }}
            </a>
          </div>

          <div v-if="entity.price">
            <div class="amount">
              {{ entity.price.currency }}
              {{ Math.round(entity.price.value) }}
            </div>
            <div class="small">
              {{ __('per person') }}
            </div>
          </div>

          <div v-else class="review">
            <i class="fa fa-star text-primary" />
            <span class="font-weight-bold">{{ entity.rating }}</span>
            <span v-if="entity.reviews_number">({{ entity.reviews_number || '' }})</span>
          </div>
        </div>

        <div
          v-if="entity.description"
          class="card-text mb-2"
        >
          {{ entity.description }}
        </div>

        <div v-if="entity.price" class="flex-body">
          <div class="experiences duration">
            <i class="fa fa-clock-o text-primary" />
            {{ durationText }}
          </div>

          <div v-if="entity.price" class="review">
            <i class="fa fa-star text-primary" />
            <span class="font-weight-bold">{{ entity.rating }}</span>
            <span v-if="entity.reviews_number">({{ entity.reviews_number || '' }})</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueGallery from 'vue-gallery'

import { mapGetters } from 'vuex'
import axios from 'axios'
import {ACTIVITY_PLACE} from '../place-types'
import { duration } from '../../../mixins/duration'

export default {
  components: {
    VueGallery
  },

  mixins: [
    duration
  ],

  props: {
    entity: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      durationRange: false,
      galleryIndex: null
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ]),

    durationText () {
      return this.processedDurationRange(this.durationRange)
    },

    images () {
      if (!this.entity.media) {
        return []
      }

      return this.entity.media
        .map(e => {
          return e.url
        })
    }
  },

  created () {
    if (this.entity.type === ACTIVITY_PLACE) {
      this.fetchDuration()
    }
  },

  methods: {
    async fetchDuration () {
      await axios
        .get(`/api/activities/${this.entity.id}/duration`)
        .then(r => {
          if (r.data.success) {
            this.durationRange = r.data.entity

            this.$emit('duration', {
              id: this.entity.id,
              duration: this.durationRange
            })
          }
        })
    },

    gridLength () {
      const length = this.images.length

      if (length < 4) {
        return length
      }

      return 4
    },

    openGallery () {
      this.galleryIndex = 0
    }
  }
}
</script>
