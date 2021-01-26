<template>
  <div class="details box">
    <div class="box-heading">
      {{ __('Schedule your time at this place of interest') }}
    </div>

    <div class="box-body">
      <div class="body-wrap">
        <div class="d-flex justify-content-between mb-4">
          <div class="form-group mr-2">
            <div
              class="cursor-pointer"
              @click.prevent="showTravellersPicker()"
            >
              <input
                :value="activeTravellersInputText"
                type="text"
                class="form-control"
                placeholder="Travellers"
                disabled
              >

              <i class="fa fa-angle-down arrow" />
            </div>

            <travellers-selector
              v-if="travellersPicker"
              v-on-clickaway="onClickOutsideTravellersPicker"
              :travellers="travellers"
              :picked-travellers="daySchedule.travellers"
              @input="onTravellersInput($event)"
              @hide="hideTravellersPicker()"
            />
          </div>
        </div>

        <div class="breakup">
          <div class="date-interval">
            <div class="d-flex justify-content-between time-row">
              <div>{{ __('Start time') }}</div>

              <div class="form-group picktime">
                <input
                  :value="startTimeInputText"
                  type="text"
                  class="form-control"
                  @click.prevent="showStartTimePicker()"
                >

                <hour-picker
                  v-if="startTimePicker"
                  v-on-clickaway="onClickOutsideStartTimePicker"
                  :timeslots="timeslots"
                  style="min-width: 100px; max-height: 160px; overflow-y: auto;"
                  @input="onStartTimeInput($event)"
                  @hide="hideStartTimePicker()"
                />
              </div>
            </div>

            <div class="d-flex justify-content-between time-row">
              <div>{{ __('End time') }}</div>

              <div class="form-group picktime">
                <input
                  :value="endTimeInputText"
                  type="text"
                  class="form-control"
                  @click.prevent="showEndTimePicker()"
                >

                <hour-picker
                  v-if="endTimePicker"
                  v-on-clickaway="onClickOutsideEndTimePicker"
                  :timeslots="timeslots"
                  style="min-width: 100px; max-height: 160px; overflow-y: auto;"
                  @input="onEndTimeInput($event)"
                  @hide="hideEndTimePicker()"
                />
              </div>
            </div>

            <div class="d-flex justify-content-between time-row">
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

        <div class="text-right">
          <button
            v-if="modify"
            class="btn btn-yellow"
            @click.prevent="$emit('modify')"
          >
            {{ __('Modify plan') }}
          </button>

          <button
            v-else
            class="btn btn-orange"
            @click.prevent="$emit('add')"
          >
            {{ __('Add to plan') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TravellersSelector from '../../components/travellers/TravellersSelector'
import HourPicker from '../../components/form/HourPicker'
import {mixin as clickaway} from 'vue-clickaway'

export default {
  components: {
    TravellersSelector,
    HourPicker
  },

  mixins: [
    clickaway
  ],

  props: {
    travellers: {
      type: Object,
      required: true
    },

    schedule: {
      type: Object,
      required: true
    },

    modify: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      activeTravellers: [],
      suggestedTime: 4,
      daySchedule: {
        travellers: {
          adults: 0,
          children: 0,
          infants: 0
        },
        startTime: '10:30',
        endTime: '10:30'
      },
      timeslots: [
        '06:00',
        '07:00',
        '08:00',
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '13:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
        '18:00',
        '19:00',
        '20:00',
        '21:00',
        '22:00',
        '23:00'
      ],
      travellersPicker: false,
      activityTravellersPicker: false,
      startTimePicker: false,
      endTimePicker: false
    }
  },

  computed: {
    activeTravellersInputText () {
      return 'Travellers ' + this.totalTravellersCount
    },

    totalTravellersCount () {
      return this.travellersCount('adults')
        + this.travellersCount('children')
        + this.travellersCount('infants')
    },

    suggestedTimeText () {
      if (!this.suggestedTime) {
        return ''
      }

      return this.suggestedTime + ' hours'
    },

    countTravellers () {
      return this.daySchedule.travellers.adults
        + this.daySchedule.travellers.children
        + this.daySchedule.travellers.infants
    },

    startTimeInputText () {
      return this.daySchedule.startTime ? this.daySchedule.startTime : ''
    },

    endTimeInputText () {
      return this.daySchedule.endTime ? this.daySchedule.endTime : ''
    }
  },

  watch: {
    schedule: {
      handler (value) {
        this.daySchedule = {
          ...value
        }
      },
      immediate: true
    }
  },

  created () {
    if (this.modify && this.schedule.travellers) {
      this.activeTravellers = this.schedule.travellers
    }

    this.processTravellers()
  },

  methods: {
    travellersCount (type) {
      return Object.values(this.daySchedule.travellers[type])
        .filter(e => {
          return e.active === true
        }).length
    },

    processTravellers () {
      const all = this.travellers

      let travellers = {
        adults: {
          //
        },
        children: {
          //
        },
        infants: {
          //
        }
      }

      for (let i = 0; i < all.adults; i++) {
        travellers.adults[i] = {
          active: this.activeTravellers.includes(`A${i + 1}`)
        }
      }

      for (let i = 0; i < all.children; i++) {
        travellers.children[i] = {
          active: this.activeTravellers.includes(`C${i + 1}`)
        }
      }

      for (let i = 0; i < all.infants; i++) {
        travellers.infants[i] = {
          active: this.activeTravellers.includes(`I${i + 1}`)
        }
      }

      this.daySchedule.travellers = {
        ...travellers
      }
    },

    onStartTimeInput (index) {
      this.daySchedule.startTime = this.timeslots[index]

      this.emitInput()
    },

    onEndTimeInput (index) {
      this.daySchedule.endTime = this.timeslots[index]

      this.emitInput()
    },

    onTravellersInput (value) {
      this.daySchedule = {
        ...this.daySchedule,
        travellers: {
          ...value
        }
      }

      this.emitInput()
    },

    onClickOutsideTravellersPicker () {
      this.travellersPicker = false
    },

    onClickOutsideActivitiesTravellersPicker () {
      this.activityTravellersPicker = false
    },

    onClickOutsideStartTimePicker () {
      this.startTimePicker = false
    },

    onClickOutsideEndTimePicker () {
      this.endTimePicker = false
    },

    showTravellersPicker () {
      this.travellersPicker = true
    },

    showActivitiesTravellersPicker () {
      this.activityTravellersPicker = true
    },

    showStartTimePicker () {
      this.startTimePicker = true
    },

    showEndTimePicker () {
      this.endTimePicker = true
    },

    hideTravellersPicker () {
      this.travellersPicker = false
    },

    hideActivitiesTravellersPicker () {
      this.activityTravellersPicker = false
    },

    hideStartTimePicker () {
      this.startTimePicker = false
    },

    hideEndTimePicker () {
      this.endTimePicker = false
    },

    emitInput () {
      this.$emit('input', {
        ...this.daySchedule,
        travellersCount: this.totalTravellersCount
      })
    }
  }
}
</script>
