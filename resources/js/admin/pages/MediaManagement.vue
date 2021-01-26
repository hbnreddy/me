<template>
  <div class="settings">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center my-2">
        <h3 class="my-4">
          Manage Media
        </h3>
      </div>

      <h4>Cities</h4>

      <hr>

      <div class="d-flex justify-content-between align-items-center my-2">
        <div style="max-width: 40%;">
          <select
            v-model="cityId"
            class="form-control mr-3"
            @change="onChangeCity()"
          >
            <option
              value="false"
              selected
            >
              No city
            </option>

            <option
              v-for="cityObj in cities.enabled"
              :key="cityObj.id"
              :value="cityObj.id"
              selected
            >
              {{ cityObj.name }}
            </option>
          </select>
        </div>

        <a
          v-if="cityId && hasImage"
          href="#"
          class="btn btn-success text-white ml-2"
          @click.prevent="save()"
        >Save changes</a>
      </div>
    </div>

    <h4
      v-if="cityId"
      class="text-center font-weight-light"
    >
      Preview of <span class="font-weight-bold">{{ city.name }}</span> in Frontend City page
    </h4>

    <div
      v-if="imageUrl"
      :style="extraStyle"
      class="cover-image"
    >
      <a
        class="text-left previous"
      >
        <div class="city">Previous City</div>
        <div class="country">Previous country</div>

        <div class="sign">
          <i class="fa fa-circle" />
          <span class="line" />
        </div>
      </a>

      <div class="text-center">
        <div class="city">
          City
        </div>
        <div class="country">
          Country
        </div>
      </div>

      <a
        class="text-left next"
      >
        <div class="city">Next City</div>
        <div class="country">Next Country</div>

        <div class="sign">
          <span class="line" />
          <i class="fa fa-circle" />
        </div>
      </a>
    </div>

    <div
      v-if="cityId"
      class="d-flex flex-column justify-content-center align-items-center"
    >
      <input
        id="fileInput"
        type="file"
        @change="uploadImage($event)"
      >

      <label
        slot="upload-label"
        for="fileInput"
        style="margin-top: 20px; text-align: center;"
      >
        <div class="cursor-point-hover">
          <i
            class="fa fa-upload"
            aria-hidden="true"
          />
        </div>

        <span class="upload-caption">{{
          hasImage ? "Replace" : "Click to upload"
        }}</span>
      </label>
    </div>
  </div>
</template>

<script>
import {mapGetters} from 'vuex'
import axios from 'axios'
import {ENABLED} from '../states'

export default {
  data () {
    return {
      msg: 'Vue Image Upload and Resize Demo',
      hasImage: false,
      imageUrl: false,
      image: false,
      cityId: false,
      city: {
      }
    }
  },

  computed: {
    ...mapGetters([
      'cities'
    ]),

    extraStyle () {
      return {
        backgroundImage: 'url(' + this.imageUrl + ')'
      }
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    this.fetchCities()

    this.$store.commit('setLoading', false)
  },

  methods: {
    uploadImage (event) {
      this.image = event.target.files[0]

      const reader = new FileReader()
      reader.readAsDataURL(this.image)

      reader.onload = e => {
        this.imageUrl = e.target.result
      }

      this.hasImage = true
    },

    async fetchCities () {
      await this.$store.dispatch('fetchCities', {
        state: ENABLED
      })
    },

    async onChangeCity () {
      this.$store.commit('setLoading', true)

      await axios
        .get(`/api/cities/${this.cityId}`)
        .then(r => {
          if (r.data.success) {
            this.city = r.data.entity

            if (this.city.image_url) {
              this.imageUrl = '/storage/' + this.city.image_url
              this.hasImage = true
            }
          } else {
            this.resetCity()
          }
        })

      this.$store.commit('setLoading', false)
    },

    async save () {
      this.$store.commit('setLoading', true)

      await axios
        .post(`/api/cities/upload-image`, {
          id: this.cityId,
          image: this.imageUrl,
          type: this.image.type
        })
        .then(r => {
          if (r.data.success) {
            this.image = {
              ...this.image,
              url: r.data.url
            }
          }
        })

      this.$store.commit('setLoading', false)
    },

    resetCity () {
      this.months.forEach(month => {
        this.city = {
          ...this.city,
          [month]: {
            month,
            high: 0,
            low: 0,
            average: 0,
            precipitation: 0,
            best_time: false
          }
        }
      })
    },

    capitalize (string) {
      return string.toString().toUpperCase()[0] + string.toString().substring(1, string.length)
        .toLowerCase()
    }
  }
}
</script>
