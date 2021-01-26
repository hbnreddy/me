<template>
  <div class="card">
    <router-link
      :to="placeRoute"
      class="text-decoration-none image-hover"
    >
      <div class="centered-text centered-text-aligned">
        {{ __('Explore') }} {{ place.name }}
      </div>

      <img
        :src="place.thumbnail_url"
        class="card-img-top"
        alt="..."
      >
    </router-link>

    <div class="card-body">
      <div class="flex-body">
        <div>
          <router-link
            :to="placeRoute"
            class="card-title"
          >
            {{ place.name }}
          </router-link>
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
            {{ query.currency.toUpperCase() }} {{ Math.round(parseFloat(lowestPrice)) }}
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
      'currentPlanId',
      'query'
    ]),

    placeRoute () {
      return {
        name: 'place',
        params: {
          cityId: this.$route.params.id,
          cityIndex: this.$route.params.index,
          placeId: this.place.id,
          planId: this.currentPlanId
        },
        query: this.$route.query
      }
    }
  },

  created () {
    this.lowestPrice = false

    if (this.place.venue) {
      this.fetches()
    }
  },

  methods: {
    async fetches () {
      this.lowestPrice = await this.$store.dispatch('fetchBestActivityPrice', this.place.venue.id)

      this.information = await this.$store.dispatch('fetchVenueInformation', {
        venueId: this.place.venue.id,
        startDate: this.query.start_date,
        endDate: this.query.end_date
      })
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
