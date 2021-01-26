<template>
  <div class="welcome-page">
    <div class="landing-page">
      <welcome-navigation />

      <video
        class="video-background"
        autoplay muted loop
      >
        <source src="../../assets/videos/lp-present.mp4" type="video/mp4">
      </video>

      <div class="container-fluid content">
        <div class="row align-items-center">
          <div class="col-lg-7 col-sm-12 col-12">
            <div class="mb-5 mb-lg-0">
              <h1 class="page-title">
                {{ __('Conveniently plan, book and manage your own\
                unique and detailed itinerary for your next holiday.') }}
              </h1>

              <div class="page-subtitle">
                {{ __('Follow 7 easy steps to create your own detailed itinerary,\
                optimized for time and cost. You can then book and manage all\
                your tickets in one place.') }}
              </div>
            </div>
          </div>

          <div class="col-lg-5 col-sm-12 col-12">
            <schedule-form
              :error="error ? error.message : false" @input="formInput($event)"
            />
          </div>
        </div>
      </div>

      <div class="more">
        <a
          v-smooth-scroll="{ duration: 1000, offset: -80 }"
          href="#how-it-works"
        >
          <i class="fa fa-angle-double-down" />
        </a>
      </div>
    </div>

    <section id="how-it-works">
      <properties />
    </section>

    <section id="browse">
      <travel-hub />
    </section>
  </div>
</template>

<script>
import WelcomeNavigation from './components/WelcomeNavbar'
import ScheduleForm from './components/ScheduleForm'
import Properties from './components/Properties'
import TravelHub from './components/TravelHub'
import { mapGetters } from 'vuex'

export default {
  components: {
    WelcomeNavigation,
    ScheduleForm,
    Properties,
    TravelHub
  },

  data () {
    return {
      query: false,
      user: null,
      error: null
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ])
  },

  created () {
    if (!this.$route.params.planId && this.currentPlan) {
      this.$router.replace({
        name: this.$route.name,
        params: {
          planId: this.currentPlan.id
        },
        query: {
          //
        }
      })
    }
  },

  methods: {
    formInput (temp) {
      this.query = temp
    }
  }
}

</script>
