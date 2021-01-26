<template>
  <div v-if="place">
    <div class="d-flex place-header justify-content-between container-fluid shadow-sm py-3">
      <div v-if="place.thumbnail_url">
        <img
          :src="place.thumbnail_url"
          class="img-fluid img-thumbnail"
          alt=""
        >
      </div>

      <div class="p-2">
        <h4 class="font-weight-bold">
          {{ place.name }}
          <span class="small text-muted">({{ place.level }})</span>
        </h4>

        <h6
          v-if="place.name_suffix"
          class="text-uppercase sufix"
        >
          {{ place.name_suffix }}
        </h6>

        <div
          v-if="place.description"
          class="description"
        >
          {{ place.description }}
        </div>

        <div v-if="place.opening_hours">
          {{ place.opening_hours }}
        </div>
      </div>

      <div class="stats p-2">
        <div class="font-weight-bold mb-1 border-bottom d-flex justify-content-between p-1">
          Is deleted:
          <span class=" text-uppercase">{{ place.is_deleted ? 'Yes' : 'No' }}</span>
        </div>

        <div class="font-weight-bold mb-1 border-bottom d-flex justify-content-between p-1">
          Is enabled:
          <span class="text-uppercase">{{ place.enabled }}</span>
        </div>

        <div class="font-weight-bold mb-1 border-bottom d-flex justify-content-between p-1">
          Local Rating:
          <div>
            <div
              v-if="place.rating_local"
              class="text-right"
            >
              Local:&nbsp;&nbsp;{{ parseFloat(place.rating_local).toFixed(2) }}
            </div>

            <div
              v-if="place.rating"
              class="text-right"
            >
              Global:&nbsp;&nbsp;{{ parseFloat(place.rating).toFixed(2) }}
            </div>

            <div
              v-if="place.star_rating"
              class="text-right"
            >
              Star:&nbsp;&nbsp;{{ parseFloat(place.star_rating).toFixed(2) }}
            </div>

            <div
              v-if="place.star_rating_unofficial"
              class="text-right"
            >
              Star unofficial: {{ parseFloat(place.star_rating_unofficial).toFixed(2) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container py-5 mb-2">
      <div v-if="place.categories">
        <h5>Categories</h5>

        <ul class="categories list-unstyled">
          <li
            v-for="(category, index) in place.categories"
            :key="index"
            class="category"
          >
            {{ category }}
          </li>
        </ul>
      </div>

      <div class="d-flex">
        <div class="flex-fill px-4">
          <table class="table">
            <tbody>
              <tr v-if="place.marker">
                <th scope="row">
                  Marker
                </th>
                <td>{{ place.marker }}</td>
              </tr>

              <tr v-if="place.theme">
                <th scope="row">
                  Theme
                </th>
                <td>{{ place.theme.name }}</td>
              </tr>

              <tr v-if="place.rutugo_categories && place.rutugo_categories.length">
                <th scope="row">
                  Rutugo category
                </th>
                <td>
                  <div
                    v-for="(category, index) in place.rutugo_categories"
                    :key="index"
                  >
                    {{ category.name }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex-fill px-4">
          <table class="table">
            <tbody>
              <tr v-if="place.location && place.location.lat">
                <th scope="row">
                  Latitude
                </th>
                <td>{{ place.location.lat }}</td>
              </tr>

              <tr v-if="place.location && place.location.lng">
                <th scope="row">
                  Longitude
                </th>
                <td>{{ place.location.lng }}</td>
              </tr>

              <tr v-if="place.bounds">
                <th scope="row">
                  Bounds
                </th>
                <td>
                  <div>
                    <span class="font-weight-bold mr-2">South: </span>
                    {{ place.bounds.south }}
                  </div>

                  <div>
                    <span class="font-weight-bold mr-2">West: </span>
                    {{ place.bounds.west }}
                  </div>

                  <div>
                    <span class="font-weight-bold mr-2">North: </span>
                    {{ place.bounds.north }}
                  </div>

                  <div>
                    <span class="font-weight-bold mr-2">East: </span>
                    {{ place.bounds.east }}
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="place.description">
        <h5>Full description</h5>

        <div class="description">
          {{ place.description.text }}
        </div>

        <a
          v-if="place.description.provider"
          :href="place.description.link"
        >Provider: {{ place.description.provider.toUpperCase() }}
        </a>
      </div>

      <div
        v-if="place.references"
        class="mb-4"
      >
        <h5>References</h5>

        <table class="table">
          <tbody>
            <tr
              v-for="(reference, index) in place.references"
              :key="index"
            >
              <th scope="row">
                {{ reference.title }}
              </th>
              <td>
                <a :href="reference.url">{{ reference.url }}</a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="place-footer d-flex justify-content-between container-fluid shadow-sm py-3">
      <div class="flex-fill px-2">
        <div class="mb-3 font-weight-bold">
          Address
        </div>
        <div v-if="place.address">
          {{ place.address }}
        </div>
        <h6 class="small text-uppercase">
          ({{ place.address_is_approximated ? 'aproximated' : 'not approximated' }})
        </h6>

        <div v-if="!place.address">
          -
        </div>
      </div>

      <div class="flex-fill px-2">
        <div class="mb-3 font-weight-bold">
          Email
        </div>
        <div v-if="place.email">
          {{ place.email }}
        </div>
      </div>

      <div class="flex-fill px-2">
        <div class="mb-3 font-weight-bold">
          Phone
        </div>
        <div v-if="place.phone">
          {{ place.phone }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
  computed: {
    ...mapGetters([
      'place'
    ]),

    placeTags () {
      if (this.place.tags[0]) {
        return this.place.tags
      }

      let tags = []
      this.place.tags.forEach(tagsArr => {
        tags = [...tags, ...tagsArr]
      })

      return tags
    }
  },

  async created () {
    if (!this.place) {
      this.$router.go(-1)
    } else if (this.place.level === 'poi') {
      await this.$store.dispatch('fetchPlace', {
        id: this.place.id
      })
    }
  }
}
</script>
