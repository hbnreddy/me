<template>
  <div
    class="v-modal"
    tabindex="-1"
  >
    <div
      class="modal modal-planner"
    >
      <div
        class="modal-dialog modal-lg"
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
              {{ __('Activities') }} & {{ __('Experiences') }}
            </h5>

            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click.prevent="hide()"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="activity">
              <div class="d-flex align-self-center mr-4">
                <i class="fa fa-puzzle-piece icon" />
              </div>

              <div class="flex-fill align-self-start">
                <div class="name">
                  {{ activity.name }}
                </div>
                <div class="subtitle">
                  {{ activity.name_suffix }}
                </div>
              </div>

              <div class="flex-fill align-self-center">
                <div class="d-flex justify-content-start">
                  <div
                    v-for="(person, personIndex) in persons"
                    :key="personIndex"
                    class="person"
                  >
                    {{ person }}
                  </div>
                </div>
              </div>

              <div class="flex-fill align-self-start text-center">
                <div class="refund px-1">
                  * {{ __('Refundable') }}
                </div>
              </div>

              <div class="flex-fill d-flex justify-content-end">
                <div class="price-selected text-center">
                  <div>$ 90</div>
                </div>
              </div>
            </div>

            <div class="details">
              <div class="details-wrap">
                <div class="details-list">
                  <div>
                    <div class="semi-bold mb-2">
                      {{ __('Fare break') }}
                    </div>

                    <div class="options">
                      <div class="option-item">
                        <div>{{ __('Adult') }}</div>
                        <div class="ml-3">
                          3
                        </div>
                        <div class="flex-fill text-right semi-bold">
                          $360
                        </div>
                      </div>

                      <div class="option-item">
                        <div>{{ __('Children') }}</div>
                        <div class="ml-3">
                          2
                        </div>
                        <div class="flex-fill text-right semi-bold">
                          $240
                        </div>
                      </div>

                      <div class="option-item">
                        <div>{{ __('Gateway charges') }}</div>
                        <div class="flex-fill text-right semi-bold">
                          $12
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="total-estimate cancel">
                    <div>{{ __('Total estimate') }}</div>
                    <div class="total">
                      $ 612
                    </div>
                  </div>
                </div>

                <div class="term-and-conditions">
                  <div class="semi-bold mb-2">
                    {{ __('Terms & conditions') }}
                  </div>

                  <ul>
                    <li
                      v-for="(cond, index) in termAndConditions"
                      :key="index"
                    >
                      {{ cond }}
                    </li>
                  </ul>
                </div>
              </div>

              <div class="btn-wrap">
                <div class="cancel-btn">
                  <button
                    data-dismiss="modal"
                    class="btn btn-orange btn-block"
                    @click.prevent="$emit('cancel')"
                  >
                    {{ __('Cancel Booking') }}
                  </button>
                </div>
              </div>
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
    activity: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      persons: [
        'AM',
        'HC'
      ],
      termAndConditions: [
        '1st Class',
        'Partially Flexible',
        'Processing fee may apply',
        'Payment in USD',
        'Exchangeable  and refundable with conditions'
      ]
    }
  },

  methods: {
    hide () {
      this.$emit('hide')
    }
  }
}
</script>
