<template>
  <div class="planner box">
    <loading-circle
      v-if="loading"
      :loading="loading"
      :text="'Loading flights and activities'"
    />

    <div v-else>
      <div class="box-heading">
        {{ __('Plan for the day') }}
      </div>

      <div v-if="!items.length" class="p-2">
        {{ __('No activities for this day') }}
      </div>

      <div class="box-body">
        <div class="activities body-wrap">
          <div
            v-for="(item, index) in items"
            :key="index"
          >
            <div
              v-if="isCartItem(item)"
              class="activity"
            >
              <div class="d-flex justify-content-between mb-3 font-weight-bold">
                <div>{{ item.name }}</div>
              </div>

              <div class="d-flex justify-content-between">
                <div class="hours">
                  <i class="fa fa-clock-o icon mr-1" />

                  {{ item.timeslot.start_time }}
                  <span v-if="item.timeslot.end_time">
                    - {{ item.timeslot.end_time }}
                  </span>
                </div>

                <travellers-row
                  :travellers="item.travellers"
                />
              </div>
            </div>

            <div
              v-else-if="item.type === 'driving'"
              class="driving"
            >
              <div class="dot-line">
                <i class="fa fa-map-marker icon" />
              </div>

              <i class="fa fa-car icon mr-1" />
              <span class="title">{{ item.title }}</span>
              <i class="fa fa-angle-right mx-2" />
            </div>

            <div
              v-else-if="item.type === 'ride'"
              class="activity flight"
            >
              <div class="d-flex justify-content-between mb-3 font-weight-bold">
                <div>
                  <span class="mr-2">
                    <i v-if="item.vehicle_type === 'aircraft'" class="fa fa-plane icon" />
                    <i v-else class="fa fa-train icon" />
                  </span>

                  <span>{{ item.from_city_code }} - {{ item.to_city_code }} ({{ printDuration(item.duration) }})</span>
                </div>

                <div v-if="item.price && 1 === 0" class="text-right">
                  {{ currency.symbol }} {{ item.price }}
                </div>
              </div>

              <div class="d-flex justify-content-between">
                <div v-if="item.timeslot" class="hours">
                  <i class="fa fa-clock-o icon mr-1" />

                  {{ item.timeslot.start_time }}
                  <span v-if="item.timeslot.end_time">
                    - {{ item.timeslot.end_time }}
                  </span>
                </div>

                <div v-if="item.temp" class="hours">
                  This may be changed in the next step
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TravellersRow from './TravellersRow'
import LoadingCircle from '../../../components/LoadingCircle'

export default {
  components: {
    TravellersRow,
    LoadingCircle
  },

  props: {
    items: {
      type: Array,
      default: () => []
    },

    currency: {
      type: Object,
      required: true
    },

    loading: {
      type: Boolean,
      default: false
    }
  },

  watch: {
    items: {
      handler () {
        //
      },
      immediate: true
    }
  },

  methods: {
    printDuration (duration) {
      const hours = Math.floor(duration / 60)
      const minutes = duration % 60

      return `${hours} hr ${minutes} min`
    },

    isCartItem (item) {
      return ['standard', 'activity'].includes(item.type)
    }
  }
}
</script>
