<template>
  <div class="travel-hub-wrapper">
    <div class="container">
      <h2 class="category-title">
        {{ __('7 easy steps for planning your next holiday') }}
      </h2>

      <div
        v-for="(hub, index) in hubs"
        :key="index"
        :data-order="index + 1"
        :class="{ 'flex-row-reverse' : index % 2 }"
        class="row line"
      >
        <div class="col-md-6">
          <div class="column">
            <img
              :src="`../../../assets/images/landingpage/${hub.image}`"
              class="img-fluid hub-image"
              alt=""
            >
          </div>
        </div>

        <div class="col-md-6">
          <div class="column">
            <div class="hub-title">
              {{ hub.title }}
            </div>

            <div class="hub-subtitle">
              {{ hub.subtitle }}
            </div>

            <div class="hub-subtitle">
              {{ hub.text }}
            </div>

            <div v-if="hub.extraText" class="hub-subtitle">
              {{ hub.extraText }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  data () {
    return {
      hubs: [
        {
          image: 'flexible-dates.png',
          title: 'Themes, Dates, Go!',
          subtitle: 'Enter details such as your preferred dates, themes, traveller information and let our research find…',
          text: '',
          extraText: ''
        },
        {
          image: 'explore.png',
          title: 'Compare and choose from a list of popular destinations to go to!',
          subtitle: 'Compare various attributes of popular destinations during your travel dates such as the number of places of interest, experiences available, best round trip flight costs, average 3 Star accommodation costs, average entrance ticket costs etc.',
          text: 'Click on your destination of interest to start planning your next holiday!'
        },
        {
          image: 'city.png',
          title: 'FOMO? We got you covered! Check out all the happening spots in a city.',
          subtitle: 'Based on your preferred travel themes and dates, we will find the most happening spots in a destination along with activities and events available at the given spots.',
          text: 'Dig in by clicking the place of interest to see what\'s happening out there!'
        },
        {
          image: 'poi.png',
          title: 'Dinner atop the Eiffel? Nice! Check out all the events and experiences at a spot.',
          subtitle: 'Check out what\'s happening at all the cool spots in the city.',
          text: 'Research on pricing, content, inclusions and exclusions for a given experience or activity or event or ticket.',
          extraText: 'Curious about what your next orange button to your right does? Only one way to find out - Click!'
        },
        {
          image: 'add-to-plan.png',
          title: 'Liked what you saw and clicked another orange button huh? Go ahead, add it to your plan!',
          subtitle: 'Here cause you liked what you saw?',
          text: 'Now that you are here, you can see what you have already added on a given date, check availability of your favorite events, activities, experiences or places of interest during the day and add them to your plan!',
          extraText: 'It\'s easy, you know what to do - click another Orange Button!'
        },
        {
          image: 'checkout.png',
          title: 'Your organized plan in one place!',
          subtitle: 'We\'ve come so far now and looks like there are only one or two more Orange Buttons as you can see.',
          text: 'Book your preferred flight tickets, hotels, events, activities and experiences and manage your bookings all in one place.',
          extraText: 'You can also download the booking vouchers, documents for flights/trains/hotels in here or find them on our iOS and Android App.'
        },
        {
          image: 'city-added.png',
          title: 'Want to add one more city to the plan? More orange buttons!',
          subtitle: 'We see you want to add a new city to your plan and we like that you want to!',
          text: 'Our flight/train search will provide you with incremental travel costs you may incur on adding a new city to your plan. You can compare the incremental costs and repeat this orange button obsession from step 3 for the new city!'
        }
      ]
    }
  }
}

</script>
