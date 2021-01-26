<template>
  <div>
    <div class="landing-page" >
      <welcome-navigation :notifications="notifications" :auth-user="data.user || false" :query="query"/>
      <video autoplay muted loop class="video-background">
        <source src="https://tripuniq.com/static/home.mp4" type="video/mp4">
      </video>
      <div class="container-fluid content">
        <div class="row align-items-center">
          <div class="col-lg-6 col-sm-12 col-12">
            <div class="mb-5 mb-lg-0">
              <h1 class="page-title">
                {{ __('PLAN EASY, TRAVEL MORE') }}
              </h1>

              <div class="page-subtitle">
                {{ __('Conveniently create a fully customizable day by day travel plan') }}
              </div>
            </div>
          </div>

          <div class="col-lg-6 col-sm-12 col-12">
            <schedule-form :static-query="query || false" :static-themes="data.themes || []" :error="error ? error.message : false" @input="formInput($event)" />
          </div>
        </div>
      </div>

      <div class="more">
        <a
          href="#how-it-works"
          v-smooth-scroll="{ duration: 1000, offset: -80 }"
        >
          <i class="fa fa-angle-double-down" />
        </a>
      </div>
    </div>

<!--    <div class="banner" style="">-->
<!--      <img src="https://trello-attachments.s3.amazonaws.com/5ef9c75c31c7e26227211db8/5f074a1ef2c3977338275d0d/052f40d3e5089f59d6e5cf425c7ccf9a/Logos_Green_Background.svg" class="banner-image">-->
<!--    </div>-->
<!--    <div class="square-nav">-->
<!--      <div class="square-nav-box"></div> </div>-->

    <section id="how-it-works">
      <properties />
    </section>

    <section id="browse">
      <travel-hub :hubs="travelHubs"/>
    </section>

    <welcome-footer/>
  </div>
</template>
<script>

import WelcomeNavigation from './components/WelcomeNavigation'
import WelcomeFooter from './components/WelcomeFooter'
import ScheduleForm from './components/ScheduleForm'
import Properties from './components/Properties'
import TravelHub from './components/TravelHub'

export default {
  components: {
    WelcomeNavigation,
    WelcomeFooter,
    ScheduleForm,
    Properties,
    TravelHub
  },

  props: {
    data: {
      type: Object,
      default: () => {

        //
      }
    },

    error: {
      type: Object,
      default: () => {}
    }
  },

  data() {
    return {
      notifications: 0,
      query: false,
      travelHubs: [
        {
          image: 'explore.png',
          title: 'Research that works for you',
          subtitle: 'Matches your travel themes with many data points to present information to you.',
          text: 'Choose destinations based on best times to visit, transport options, stay costs, Places of Interests and Experiences available during your dates. Our unique flexible date options allow you to search for the best options during given dates.'
        },
        {
          image: 'city.png',
          title: 'Thematic research that helps you',
          subtitle: 'Identify your themes of travel.',
          text: 'Research the top spots, places of interest a city has to offer. View all activities available in relation to a Place of Interest, its Content and Pricing.'
        },
        {
          image: 'poi.png',
          title: 'Add your preferred places of interest to your plan',
          subtitle: 'We identify all the best activities available on the internet for your preferred Place of Interest.',
          text: 'Research the activities available and other places of interest covered in an activity. Add the Places of Interest along with the activity to your wish list.'
        },
        {
          image: 'checkout.png',
          title: 'Your optimized plan in one place',
          subtitle: 'Share and collaborate with co-travelers.',
          text: 'Optimize your itinerary, minimize travel time and cost. Say hi to your new travel hub! Book and Manage documents, cancellations, refunds, rescheduling - all in one place.'
        }
      ]
    }
  },

  created() {
    this.notifications = this.data.notifications
  },
  methods: {
    formInput(temp) {
      this.query = temp
    }
  }
}

</script>
