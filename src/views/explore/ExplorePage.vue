<template>
  <div class="explore-page">
    <cities-list />
  </div>
</template>

<script>
import CitiesList from './components/CitiesList'
import axios from 'axios'
import { API_URL } from '../../bootstrap/constants'
export default {
  components: {
    CitiesList
  },
  //  created () {
  //    axios
  //           .get(`${API_URL}/get-explore`, {
  //               params: {
  //                 currency:this.$route.query.currency,
  //                 start_date:this.$route.query.start_date,
  //                 end_date:this.$route.query.end_date,
  //                   origin_city_id: this.$route.query.origin_city_id,
  //                   date_type: this.$route.query.date_type,
  //                   themes: this.$route.query.themes,
  //                   travellers:this.$route.query.travellers,
  //               }
  //           })
  //           .then(r => {
  //             console.log(r,"data")
  //             if (r.user) {
  //               this.$store.commit('setAuthUser', r.user)
  //             }

  //             this.$store.commit('setCities', r.cities)
  //             this.$store.commit('setNotifications', r.notifications)
  //             this.$store.commit('setCurrentPlanId', r.current_plan_id)
  //             this.$store.commit('setThemes', r.themes)
  //             this.$store.commit('setQuery', r.query)
  //           })
  // }
}
</script>
