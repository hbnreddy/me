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
          :places="presentPlaces"
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
import axios from 'axios'
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
      'currentPlanId'
    ]),

    computedPlaces () {
      if (this.addedToCart) {
        return this.cartPlaces
      }

      return this.places
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
      }
    },

    addedToCart: {
      async handler (value) {
        this.presentPlaces = []

        if (value) {
          await this.fetchAddedPlaces()

          this.offset = 0
          this.currentPage = 1
          this.totalPages = Math.ceil(this.cartPlaces.length / this.perPageNumber)

          this.presentPlaces = this.cartPlaces
        } else {
          await this.fetches()
        }
      },
      immediate: true
    }
  },

  methods: {
    async fetches () {
      this.loading = true

      await this.fetchTotalNumber()

      await this.fetchPlaces()

      this.currentPage = this.currentPage.toString().padStart(2, 0)

      this.loading = false
    },

    async fetchTotalNumber () {
      await axios
        .get(`/api/cities/${this.cityId}/counts`, {
          params: {
            themes: this.theme.id
          }
        })
        .then(r => {
          this.totalPages = Math.ceil(r.data.places_count / this.perPageNumber)
        })
    },

    pageChanged (page) {
      this.currentPage = page

      this.fetchPlaces()
    },

    async fetchPlaces () {
      await axios
        .get(`/api/cities/${this.$route.params.id}/places`, {
          params: {
            limit: this.limit,
            offset: this.offset,
            theme_id: this.theme.id
          }
        })
        .then(r => {
          if (r.data.success) {
            this.places = [
              ...this.places,
              ...r.data.entities
            ]

            this.presentPlaces = this.places

            this.offset += r.data.entities.length
          }
        })
    },

    async fetchAddedPlaces () {
      this.offset = 0

      if (!this.currentPlanId) {
        this.cartPlaces = []

        return false
      }

      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/items-ids`, {
          params: {
            theme_id: this.theme.id
          }
        })
        .then(async (r) => {
          if (r.data.success) {
            await axios
              .get(`/api/places/by-ids`, {
                params: {
                  ids: r.data.entities.join('-'),
                  theme_id: this.theme.id,
                  city_id: this.cityId
                }
              })
              .then(res => {
                this.cartPlaces = res.data.entities
              })
          }
        })
    }
  }
}
</script>
