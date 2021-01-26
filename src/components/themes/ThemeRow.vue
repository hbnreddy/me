<template>
  <div v-if="computedPlaces.length" class="row theme-row">
    <div class="col-md-5 col-lg-4 align-self-stretch">
      <div class="carousel-description">
        <h4 class="title">
          {{ theme.name }}
        </h4>

        <div
          v-if="theme.description"
          class="text"
        >
          {{ theme.description }}
        </div>

        <div class="slide-pagination">
          <span class="page">{{ currentPage }}</span>
          <span class="total-pages">{{ printPagesNumber }}</span>

          <div class="progress">
            <div
              :aria-valuenow="currentProgress"
              :style="'width:' + currentProgress + '%'"
              class="progress-bar"
              role="progressbar"
              aria-valuemin="0"
              aria-valuemax="100"
            />
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-7 col-lg-8">
      <div class="carousel">
        <theme-carousel
          :pages="totalPages"
          :places="computedPlaces"
          @page-changed="pageChanged($event)"
        />
      </div>
    </div>
  </div>

  <div v-else>
    <loading-circle
      :loading="loading"
      :small="false"
      :color="'#FD753B'"
    />
  </div>
</template>

<script>
import ThemeCarousel from './ThemeCarousel'

import { mapGetters } from 'vuex'

export default {
  components: {
    ThemeCarousel
  },

  props: {
    theme: {
      type: Object,
      required: true
    },

    cityId: {
      type: Number,
      required: true
    },

    addedToCart: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      cartPlaces: [],
      presentPlaces: [],
      added: false,
      places: [],
      limit: 6,
      offset: 0,
      totalPages: 0,
      currentPage: 1,
      loading: false
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ]),

    computedPlaces () {
      if (this.addedToCart) {
        return this.places.filter(e => {
          return this.addedItems.includes(e.id.toString())
        })
      }

      return this.places.filter(e => {
        return !this.addedItems.includes(e.id.toString())
      })
    },

    addedItems () {
      if (!this.currentPlan) {
        return []
      }

      return this.currentPlan.items.map(e => {
        return ['standard', 'activity'].includes(e.type) && e.id.toString()
      })
    },

    printPagesNumber () {
      const pages = this.totalPages

      if (pages < 10) {
        return pages.toString().padStart(2, 0)
      }

      return pages
    },

    perPageNumber () {
      let itemsPerPage = 3

      if (window.matchMedia('(max-width: 525px)').matches) {
        itemsPerPage = 1
      } else if (window.matchMedia('(max-width: 1024px)').matches) {
        itemsPerPage = 2
      }

      return itemsPerPage
    },

    currentProgress () {
      if (this.totalPages) {
        return this.currentPage / this.totalPages * 100
      }

      return 0
    }
  },

  watch: {
    cityId: {
      handler () {
        this.fetches()
      },
      immediate: true
    }
  },

  methods: {
    async fetches () {
      this.places = []

      this.loading = true

      await this.fetchPlaces()

      await this.fetchPlacesCount()

      this.currentPage = this.currentPage.toString().padStart(2, 0)

      this.loading = false
      this.$emit('loaded', this.theme.id)
    },

    async fetchPlacesCount () {
      const counts = await this.$store.dispatch('fetchCityCounts', {
        id: this.cityId,
        themes: this.theme.id
      })

      this.totalPages = Math.ceil(counts.places_count / this.perPageNumber)
    },

    pageChanged (page) {
      this.currentPage = page

      this.fetchPlaces()
    },

    async fetchPlaces () {
      this.places = [
        ...this.places,
        ... await this.$store.dispatch('fetchPlaces', {
          cityId: this.cityId,
          limit: this.limit,
          offset: this.offset,
          theme_id: this.theme.id
        })
      ]

      this.presentPlaces = this.places

      this.offset = this.places.length
    }
  }
}
</script>
