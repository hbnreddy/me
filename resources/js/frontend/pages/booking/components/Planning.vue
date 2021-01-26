<template>
  <div class="plans">
    <div class="tabs-wrap">
      <ul
        id="myTab"
        class="nav nav-tabs"
        role="tablist"
      >
        <li class="nav-item">
          <a
            id="experinces-tab"
            class="nav-link active"
            data-toggle="tab"
            href="#experinces"
            role="tab"
            aria-controls="experinces"
            aria-selected="false"
            v-on:click="handleClick"
          >{{ __('Activities') }} & {{ __('Experiences') }}</a>
          <img class="img-review img-experinces-tab" src="/logo/check.png" >
        </li>

        <li class="nav-item">
          <a
            id="travel-tab"
            class="nav-link"
            data-toggle="tab"
            href="#travel"
            role="tab"
            aria-controls="travel"
            aria-selected="true"
            v-on:click="handleClick"
          >{{ __('Travel') }} </a>
          <img class="img-review img-travel-tab" src="/logo/check-grey.png" >

        </li>

        <li class="nav-item">
          <a
            id="stay-tab"
            class="nav-link"
            data-toggle="tab"
            href="#stay"
            role="tab"
            aria-controls="stay"
            aria-selected="false"
            v-on:click="handleClick"
          >{{ __('Stay') }}</a>
          <img class="img-review img-stay-tab" src="/logo/check-grey.png" >
        </li>
        <li class="nav-item">
          <a
            id="traveller-details-tab"
            class="nav-link"
            data-toggle="tab"
            href="#stay"
            role="tab"
            aria-controls="stay"
            aria-selected="false"
            v-on:click="handleClick"
          >{{ __('Travellers Details') }}</a>
          <img class="img-review img-traveller-details-tab" src="/logo/check-grey.png" >
        </li>
      </ul>
    </div>

    <div
      id="myTabContent"
      class="tab-content"
    >
      <div
        id="experinces"
        class="tab-pane fade show active"
        role="tabpanel"
        aria-labelledby="experinces-tab"
      >
        <activities-tab-content />
      </div>

      <div
        id="travel"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="travel-tab"
      >
        <travel-tab-content />
      </div>

      <div
        id="stay"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="stay-tab"
      >
        <hotels-tab-content />
      </div>
      <div
        id="traveller-details"
        class="tab-pane fade"
        role="tabpanel"
        aria-labelledby="traveller-details-tab"
      >
        <hotels-tab-content />
      </div>
    </div>
  </div>
</template>

<script>
import TravelTabContent from './travel/TravelTabContent'
import HotelsTabContent from './stay/HotelsTabContent'
import ActivitiesTabContent from '../../checkout/ActivitiesTabContent'
export default {
  components: {
    TravelTabContent,
    ActivitiesTabContent,
    HotelsTabContent
  },
  methods: {
    handleClick(current) {
      $('.img-review.img-' + current.target.id).attr('src','/logo/check.png')
    }
  }
}
</script>
