<template>
  <div class="place-standard">
    <place-header
      :entity="entity"
    />

    <place-details
      :entity="entity"
    />

    <slot name="below-details" />

    <slot name="above-stories" />

    <travellers-stories
      v-if="mediaResources.length"
      :media-resources="mediaResources"
    />

    <slot name="below-stories" />

    <similar-places
      v-if="similarPlaces.length"
      :places="similarPlaces"
    />
  </div>
</template>

<script>
import PlaceHeader from './PlaceHeader'
import PlaceDetails from './PlaceDetails'
import TravellersStories from '../TravellersStories'
import SimilarPlaces from './SimilarPlaces'

import axios from 'axios'

export default {
  components: {
    PlaceDetails,
    SimilarPlaces,
    TravellersStories,
    PlaceHeader
    //
  },

  props: {
    entity: {
      type: Object,
      required: true
    },

    isActivity: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      mediaResources: [],
      similarPlaces: []
    }
  },

  created () {
    this.fetchMedia()
    this.fetchSimilarPlaces()
  },

  methods: {
    async fetchSimilarPlaces () {
      await axios
        .get(`/api/places`, {
          params: {
            city_id: this.entity.identifiers.city_id,
            marker_id: this.entity.identifiers.marker_id,
            limit: 8
          }
        })
        .then(r => {
          if (r.data.success) {
            this.similarPlaces = r.data.entities.filter(e => {
              return parseInt(e.id) !== parseInt(this.entity.identifiers.place_id)
            })
          }
        })
    },

    async fetchMedia () {
      if (this.isActivity) {
        await axios
          .get(`/api/activities/${this.entity.id}/media`)
          .then(r => {
            if (r.data.success) {
              this.mediaResources = r.data.entities
            }
          })
      } else {
        this.mediaResources = this.entity.media
      }
    }
  }
}
</script>
