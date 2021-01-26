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
        style="margin-top: 10%;"
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
            <div class="term-and-conditions">
              <ul>
                <li
                  v-for="(cond, index) in terms"
                  :key="index"
                >
                  {{ cond }}
                </li>
              </ul>
            </div>

            <div class="text-center">
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
