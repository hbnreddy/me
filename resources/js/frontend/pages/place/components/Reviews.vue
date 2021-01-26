<template>
  <div class="reviews">
    <h6 class="font-weight-bold mb-3">
      {{ __('Travellers reviews') }}

      <span class="ml-3">
        <i class="fa fa-star text-primary mx-1" />
        <span class="font-weight-bold">&nbsp;{{ averageRating }}</span>
        ({{ reviews.length }})
      </span>
    </h6>

    <div class="review-list">
      <div
        v-for="(review, reviewIndex) in reviews"
        :key="reviewIndex"
        class="review"
      >
        <div class="heading">
          <div class="avatar mr-3">
            <img
              :src="review.author.avatar"
              alt="Avatar"
            >
          </div>

          <div class="name mr-3">
            {{ review.author.firstname }}
            <div class="small">
              {{ date(review.sent_at) }}
            </div>
          </div>

          <div class="stars">
            <i
              v-for="(rating, ratingIndex) in processReviewRating(review.rating)"
              :key="ratingIndex"
              :class="rating.rate ? 'fa-star' : 'fa-star-o'"
              class="fa text-primary mx-1"
            />
          </div>
        </div>

        <div
          v-if="review.body && review.body !== ''"
          class="text"
        >
          {{ review.body }}
        </div>
      </div>
    </div>

    <nav
      aria-label="..."
      class="d-none"
    >
      <ul class="pagination justify-content-center pagination-sm">
        <li class="page-item prev disabled">
          <span class="page-link">
            <i class="fa fa-angle-left" />
            <i class="fa fa-angle-left" />
          </span>
        </li>

        <li class="page-item active">
          <a
            class="page-link"
            href="#"
          >1</a>
        </li>

        <li class="page-item next">
          <a
            class="page-link"
            href="#"
          >
            <i class="fa fa-angle-right" />
            <i class="fa fa-angle-right" />
          </a>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
import moment from 'moment'

export default {
  props: {
    reviews: {
      type: Array,
      default: () => []
    },

    averageRating: {
      type: Number | String,
      default: () => ''
    }
  },

  methods: {
    date (date) {
      return moment(date).format('ddd, D MMM YYYY')
    },

    processReviewRating (rating) {
      if (!rating) {
        return []
      }

      let ratings = []
      for (let i = 0; i < 5; ++i) {
        ratings.push({
          rate: false
        })
      }
      for (let i = 0; i < rating; ++i) {
        ratings[i].rate = true
      }


      return ratings
    }
  }
}
</script>
