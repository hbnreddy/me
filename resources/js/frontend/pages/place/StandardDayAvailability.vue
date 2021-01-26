<template>
  <div class="details box">
    <div class="box-heading">
      {{ __('Schedule your time at this place of interest') }}
    </div>

    <div
      class="box-body"
      style="overflow-x: hidden;"
    >
      <div class="body-wrap flex-column">
        <div class="d-flex justify-content-between mb-4">
          <div class="form-group mr-2">
            <travelers-input
              :value="travellers"
              @input="onInputTravelers($event)"
            />
          </div>
        </div>

        <div class="breakup">
          <div class="date-interval">
            <div class="d-flex justify-content-between time-row">
              <div>{{ __('Start time') }}</div>

              <div class="form-group picktime">
                <timeslot-input
                  :value="timeslots[startIndex]"
                  :timeslots="timeslots"
                  @input="onInputStartTime($event)"
                />
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center time-row">
              <div>{{ __('End time') }}</div>

              <div class="form-group picktime">
                <timeslot-input
                  :value="timeslots[endIndex]"
                  :timeslots="timeslots"
                  @input="onInputEndTime($event)"
                />
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center time-row">
              <div>{{ __('Suggested time') }}</div>

              <div class="form-group picktime">
                <input
                  :value="suggestedTimeText"
                  type="text"
                  class="form-control"
                  placeholder="4 hours"
                  disabled
                >
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end">
          <button
            v-if="modify"
            class="btn btn-yellow d-flex justify-content-center align-items-center"
            @click.prevent="update()"
          >
            <loading-circle
              :loading="btnLoading"
              :small="true"
              :color="'#ffffff'"
              class="mr-2"
            />

            {{ __(btnText ? btnText : 'Modify plan') }}
          </button>

          <button
            v-else
            class="btn btn-orange d-flex justify-content-center align-items-center blob orange"
            @click.prevent="update()"
          >
            <loading-circle
              :loading="btnLoading"
              :small="true"
              :color="'#ffffff'"
              class="mr-2"
            />

            {{ __(btnText ? btnText : 'Add to plan') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import LoadingCircle from '../../../components/LoadingCircle'
import TravelersInput from './TravelersInput'

import {mixin as clickaway} from 'vue-clickaway'
import TimeslotInput from './TimeslotInput'

export default {
  components: {
    LoadingCircle,
    TimeslotInput,
    TravelersInput
  },

  mixins: [
    clickaway
  ],

  props: {
    travellers: {
      type: Array,
      default: () => []
    },

    timeslots: {
      type: Array,
      default: () => []
    },

    timeslot: {
      type: Object,
      required: true
    },

    modify: {
      type: Boolean,
      default: false
    },

    btnLoading: {
      type: Boolean,
      default: false
    },

    btnText: {
      type: String,
      default: () => ''
    }
  },

  data () {
    return {
      startIndex: 0,
      endIndex: 1,
      suggestedTimeText: '4 hours',
      startTimePicker: false,
      endTimePicker: false
    }
  },

  computed: {
    startTimeText () {
      return this.timeslot.start_time
    },

    endTimeText () {
      return this.timeslot.end_time
    }
  },

  watch: {
    timeslot: {
      handler () {
        if (this.timeslot.start_time) {
          this.startIndex = this.timeslots.findIndex(e => {
            return e.start_time === this.timeslot.start_time
          })
        }

        if (this.timeslot.end_time) {
          this.endIndex = this.timeslots.findIndex(e => {
            return e.start_time === this.timeslot.end_time
          })
        }
      },
      immediate: true
    }
  },

  methods: {
    update () {
      this.$emit('update')
    },

    onInputTravelers (travelers) {
      this.$emit('travellers', [
        ...travelers
      ])
    },

    onInputStartTime (index) {
      this.startIndex = index

      this.$emit('timeslot', {
        ...this.timeslot,
        start_time: this.timeslots[index].start_time
      })

      this.hidePicker()
    },

    onInputEndTime (index) {
      this.endIndex = index

      this.$emit('timeslot', {
        ...this.timeslot,
        end_time: this.timeslots[index].start_time
      })

      this.hidePicker()
    },

    hidePicker () {
      this.startTimePicker = false
      this.endTimePicker = false
    }
  }
}
</script>
