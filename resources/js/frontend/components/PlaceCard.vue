<template>
  <div class="card">
    <a
      href="#"
      class="text-decoration-none image-hover"
      @click.prevent="goToRoute()"
    >
      <div class="centered-text centered-text-aligned">
        {{ __('Explore') }} {{ place.name }}
      </div>

      <img
        v-onload="place.thumbnail_url"
        class="card-img-top"
        alt="..."
      >
    </a>

    <div class="card-body">
      <div class="flex-body">
        <div>
          <a
            href="#"
            class="card-title"
            @click.prevent="goToRoute()"
          >
            {{ place.name }}
          </a>
          <div class="card-text">
            <span v-if="fullDescription[place.id]">
              {{ place.short_description }}
            </span>

            <span v-else>
              {{ shortText(place.short_description) }}
            </span>

            <a
              v-if="fullDescription[place.id]"
              href="#"
              class="text-decoration-none"
              @click.prevent="showFullDescription(place.id, false)"
            >{{ __('Show less') }}
            </a>

            <a
              v-else
              href="#"
              class="text-decoration-none"
              @click.prevent="showFullDescription(place.id, true)"
            >{{ __('Show more') }}
            </a>
          </div>
        </div>

        <div v-if="lowestPrice">
          <div class="amount">
            {{ query.currency.symbol }} {{ Math.round(parseFloat(lowestPrice)) }}
          </div>

          <div class="small">
            {{ __('per person') }}
          </div>
        </div>

        <div
          v-else-if="place.venue && information.events_count"
          class="small text-center"
        >
          {{ __('Calculating price') }}..
          <img
            src="/loading.gif"
            width="24px"
            height="24px"
            alt="loading"
          >
        </div>
      </div>

      <div class="flex-body">
        <div
          v-if="place.venue && information.events_count"
          class="experiences"
        >
          {{ information.events_count }} {{ __('Experiences') }}
        </div>

        <div
          v-if="place.venue"
          class="review"
        >
          <i class="fa fa-star text-primary" />
          <span class="font-weight-bold">{{ place.rating.toFixed(1) }}</span>
          ({{ information.reviews_avg || 0 }})
        </div>

        <div
          v-else-if="place.rating"
          class="review text-right w-100"
        >
          <i class="fa fa-star text-primary" />
          <span class="font-weight-bold">{{ place.rating.toFixed(1) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters } from 'vuex'
import { route } from '../mixins/route'

export default {
  mixins: [
    route
  ],

  props: {
    place: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      information: false,
      lowestPrice: false,
      fullDescription: {
      }
    }
  },

  computed: {
    ...mapGetters([
      'query'
    ])
  },

  created () {
    this.lowestPrice = false

    if (this.place.venue) {
      axios
        .get(`/api/venues/${this.place.venue.id}/best-activity-price`)
        .then(r => {
          if (r.data.success) {
            this.lowestPrice = r.data.entity
          }
        })

      axios
        .get(`/api/venues/${this.place.venue.id}/information`)
        .then(r => {
          this.information = r.data.entity
        })
    }
  },

  methods: {
    goToRoute () {
      this.$redirect(`place/${this.place.id}`, this.routeParams)
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

      return text.substr(0, 42) + '..'
    }
  }
}
</script>
