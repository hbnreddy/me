<template>
  <div class="settings">
    <div class="container">
      <div
        v-if="showStatus"
        class="alert alert-primary my-2"
        role="alert"
      >
        Text boxes have been updated.
      </div>

      <div class="d-flex justify-content-between align-items-center my-2">
        <h3 class="my-4">
          Settings
        </h3>
        <hr>
      </div>

      <div class="card p-4">
        <div class="d-flex justify-content-between">
          <h4 class="mb-4">
            Landing page - Text boxes
          </h4>

          <a
            href="#"
            @click.prevent="addTextBox()"
          >
            <i
              class="fa fa-plus-circle"
              style="
                 font-size: 28px;
                 color: #0C66F1;
              "
            />
          </a>
        </div>
        <hr>

        <div class="d-flex mb-3">
          <div
            v-for="(textBox, index) in settings.text_boxes"
            :key="index"
            class="card mr-4"
            style="width: 18rem;"
          >
            <div class="text-right p-1">
              <a
                href="#"
                @click.prevent="destroy(index)"
              >
                <i
                  class="fa fa-minus-circle"
                  style="
                 font-size: 20px;
                 color: crimson;
              "
                />
              </a>
            </div>

            <div class="card-body">
              <textarea
                v-model="textBox.content"
                class="form-control mb-3"
              />
            </div>
          </div>
        </div>

        <div class="text-right">
          <a
            href="#"
            class="btn btn-outline-primary"
            @click.prevent="update()"
          >Update text boxes
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      settings: {
        text_boxes: []
      },
      showStatus: false
    }
  },

  created () {
    this.$store.commit('setLoading', true)

    this.fetchSettings()

    this.$store.commit('setLoading', false)
  },

  methods: {
    async fetchSettings () {
      await axios
        .get(`/admin/settings`)
        .then(r => {
          this.settings = r.data.settings
        })

      if (!Object.keys(this.settings).length) {
        this.settings = {
          text_boxes: []
        }
      }
    },

    addTextBox () {
      this.settings.text_boxes.push({
        content: ''
      })
    },

    async update () {
      this.showStatus = true
      this.$store.commit('setLoading', true)

      await axios
        .post(`/admin/settings/store`, {
          ...this.settings
        })

      this.$store.commit('setLoading', false)

      setTimeout(() => {
        this.showStatus = false
      }, 2000)
    },

    destroy (index) {
      this.settings.text_boxes.splice(index, 1)
    }
  }
}
</script>
