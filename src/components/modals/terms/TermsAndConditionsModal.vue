<template>
  <div
    class="modal term-and-conditions-modal"
    tabindex="-1"
    @keydown.esc="hide()"
  >
    <div
      class="modal"
    >
      <div
        class="modal-dialog modal-md"
        role="document"
      >
        <div
          v-on-clickaway="hide"
          class="modal-content"
        >
          <div class="modal-header">
            <h5
              id="exampleModalLabel"
              class="modal-title"
            >
              {{ __('Terms and Conditions') }}
            </h5>

            <button
              type="button"
              class="close"
              @click.prevent="hide()"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div
            class="modal-body"
            style="
              display: block;
              max-height: 660px;
              overflow-y: auto;
              overflow-x: hidden;
            "
          >
            <div class="terms-block">
              <ul
                class="terms-list"
              >
                <li
                  v-for="(term, index) in terms"
                  :key="index"
                  class="terms-item"
                >
                  <p class="name">
                    <a
                      v-if="term.type === 'url'"
                      :href="term.content"
                      :class="term.type"
                      target="_blank"
                    >
                      {{ term.name }}
                    </a>

                    <span v-else class="text font-weight-bold">{{ term.name }}</span>
                  </p>

                  <p v-if="term.type !== 'url'" class="content">
                    <span>{{ term.content }}</span>
                  </p>
                </li>
              </ul>
            </div>

            <div class="text-right">
              <button
                class="btn btn-orange"
                @click.prevent="hide()"
              >
                {{ __('Continue Booking') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal-backdrop" style="opacity: 0.5;" />
  </div>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    terms: {
      type: Array,
      default: () => []
    }
  },

  methods: {
    hide () {
      this.$emit('hide')
    }
  }
}
</script>
