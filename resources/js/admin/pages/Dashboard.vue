<template>
  <div class="container-fluid">
    <div class="d-flex flex-column">
      <div class="statistics">
        <div class="statistics-box">
          <p class="title">
            Total users
          </p>

          <div class="content">
            <div class="icon">
              <i class="fa fa-users" />
            </div>

            <p class="result">
              {{ statistics.total_users }}
            </p>
          </div>
        </div>

        <div class="statistics-box">
          <p class="title">
            Total places
          </p>

          <div class="content">
            <div class="icon">
              <i class="fa fa-map-marker" />
            </div>

            <p class="result">
              {{ statistics.total_places }}
            </p>
          </div>
        </div>

        <div class="statistics-box">
          <p class="title">
            Total themes
          </p>

          <div class="content">
            <div class="icon">
              <i class="fa fa-edit" />
            </div>

            <p class="result">
              {{ statistics.total_themes }}
            </p>
          </div>
        </div>
      </div>

      <div class="sidebar-stats">
        <div class="side-box">
          <div class="text">
            <i class="fa fa-globe" />
            <span>{{ statistics.active_continents }}</span> active continents

            <div class="text-right">
              <span class="special">(total of {{ statistics.total_continents }})</span>
            </div>
          </div>
        </div>

        <div class="side-box">
          <div class="text">
            <i class="fa fa-paper-plane" />
            <span>{{ statistics.active_countries }}</span> active countries

            <div class="text-right">
              <span class="special">(total of {{ statistics.total_countries }})</span>
            </div>
          </div>
        </div>

        <div class="side-box">
          <div class="text">
            <i class="fa fa-location-arrow" />
            <span>{{ statistics.active_cities }}</span> active cities

            <div class="text-right">
              <span class="special">(total of {{ statistics.total_cities }})</span>
            </div>
          </div>
        </div>

        <div class="side-box">
          <div class="text">
            <i class="fa fa-thumb-tack" />
            <span>{{ statistics.assigned_markers }}</span> assigned markers

            <div class="text-right">
              <span class="special">(total of {{ statistics.total_markers }})</span>
            </div>
          </div>
        </div>
      </div>

      <order-statistics/>
    </div>
  </div>
</template>

<script>
import OrderStatistics from './OrderStatistics'

import axios from 'axios'

export default {
  components: {
    OrderStatistics
  },

  data () {
    return {
      statistics: {
      }
    }
  },

  async created () {
    this.$store.commit('setLoading', true)

    await axios
      .get(`/api/statistics`)
      .then(r => {
        this.statistics = r.data.entities
      })

    this.$store.commit('setLoading', false)
  }
}
</script>
