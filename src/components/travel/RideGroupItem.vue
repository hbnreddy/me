<template>
  <li class="flight-item">
    <div class="header">
      <p class="title-block">
        <span class="title">Flight {{ index + 1 }}</span>
        <small class="path">{{ item.origin_code }} - {{ item.destination_code }}</small>
      </p>

      <p class="duration">
        {{ durationString(item.duration) }}
      </p>
    </div>

    <div class="box box-border flight-box">
      <div
        v-for="(segment, segmentIndex) in item.paths"
        :key="segmentIndex"
        class="date-block"
      >
        <div class="date">
          {{ moment(segment.timeslot.start_date, 'DD/MM/YYYY-HH:mm').format('ddd, d MMM') }}
        </div>

        <div class="content">
          <div class="left">
            <div class="timing">
              {{ moment(segment.timeslot.start_date, 'DD/MM/YYYY-HH:mm').format('HH:mm') }}
              - {{ moment(segment.timeslot.end_date, 'DD/MM/YYYY-HH:mm').format('HH:mm') }}
            </div>

            <p class="path">
              {{ segment.origin.code }} - {{ segment.destination.code }}
            </p>
          </div>

          <div class="right">
            <div class="top">
              <div class="travel-class">
                {{ segment.travel_class.tf_class }}
                ({{ segment.travel_class.supplier_class }})
              </div>

              <div class="duration">
                {{ durationString(segment.duration) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <section class="actions">
        <a
          class="btn-terms mr-3"
          @click.prevent="toggleTerms()"
        >
          Terms & Conditions
        </a>

        <loading-circle
          :loading="loading"
          :small="true"
          :color="'#FD753B'"
          class="mr-3"
        />
      </section>
    </div>

    <terms-and-conditions-modal
      v-if="showTerms && !loading"
      :terms="terms"
      @hide="showTerms = false"
    />
  </li>
</template>

<script>
import TermsAndConditionsModal from '../modals/terms/TermsAndConditionsModal'
import LoadingCircle from '../loading/LoadingCircle'

export default {
  components: {
    TermsAndConditionsModal,
    LoadingCircle
  },

  props: {
    index: {
      type: Number,
      required: true
    },

    item: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      loading: false,
      terms: [],
      requested: false,
      showTerms: false
    }
  },

  methods: {
    async toggleTerms () {
      this.loading = true

      if (!this.terms.length && !this.requested) {
        this.terms = await this.$store.dispatch('fetchFlightTerms', {
          routingId: this.item.routing_id,
          itemId: this.item.id
        })

        this.requested = true
      }

      this.showTerms = !this.showTerms
      this.loading = false
    },

    durationString (duration) {
      const hours = parseInt(duration / 60)
      const minutes = duration - (60 * hours)

      let str = ''
      if (hours) {
        str = `${hours}h`
      }

      if (minutes) {
        if (str) {
          str = `${str} ${minutes}m`
        } else {
          str = `${minutes}m`
        }
      }

      return str
    }
  }
}
</script>
