<template>
  <carousel
    :per-page-custom="[[320, 1], [526, 2], [1025, 3]]"
    :mouse-drag="true"
    :autoplay="false"
    :pagination-enabled="false"
    :navigation-enabled="true"
    :navigation-next-label="'<i class=\'fa fa-angle-right slide-next\'></i>'"
    :navigation-prev-label="'<i class=\'fa fa-angle-left slide-prev\'></i>'"
    @pageChange="changeCurrentPage($event)"
  >
    <slide
      v-for="(place, index) in places"
      v-if="places.length"
      :key="index"
    >
      <place-card
        :place="place"
      />
    </slide>

    <slide
      v-for="i in 3"
      v-if="!places.length"
      :key="i"
    >
      <card-loading />
    </slide>
  </carousel>
</template>

<script>
import PlaceCard from './PlaceCard'
import CardLoading from './CardLoading'

export default {
  components: {
    CardLoading,
    PlaceCard
  },

  props: {
    places: {
      type: Array,
      default: () => []
    },

    pages: {
      type: Number,
      required: true
    }
  },

  methods: {
    changeCurrentPage (dataset) {
      this.currentPage = (++dataset).toString()
      this.currentProgress = this.currentPage / this.pages * 100

      if (this.currentPage < 10) {
        this.currentPage = this.currentPage.padStart(2, 0)
      }

      this.$emit('page-changed', this.currentPage)
    }
  }
}
</script>
