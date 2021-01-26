<template>
  <div class="booking-steps">
    <div class="plans">
      <div class="tabs-wrap">
        <ul
          class="nav nav-tabs"
        >
          <tab-item
            v-for="(tab, index) in tabs"
            :key="index"
            :tab="tab"
            :active="tab.key === currentTab"
            @select="currentTab = $event"
          />
        </ul>
      </div>

      <div class="tab-content">
        <keep-alive>
          <component :is="`${currentTab}-tab-content`" />
        </keep-alive>
      </div>
    </div>
  </div>
</template>

<script>
import TabItem from './TabItem'
import ActivitiesTabContent from '../planning-tabs/ActivitiesTabContent'
import TravelTabContent from '../planning-tabs/TravelTabContent'
import HotelsTabContent from '../planning-tabs/HotelsTabContent'
import TravellersTabContent from '../planning-tabs/TravellersTabContent'

export default {
  components: {
    TabItem,
    TravelTabContent,
    ActivitiesTabContent,
    HotelsTabContent,
    TravellersTabContent
  },

  data () {
    return {
      tabs: [
        {
          text: 'Activities & Experiences',
          key: 'activities'
        },
        {
          text: 'Travel',
          key: 'travel'
        },
        {
          text: 'Stay',
          key: 'hotels'
        },
        {
          text: 'Travellers',
          key: 'travellers'
        }
      ],
      currentTab: 'activities'
    }
  }
}
</script>
