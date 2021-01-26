<template>
  <div class="planning">
    <div class="container-fluid">
      <div class="planning-content place-of-interest">
        <div class="title">
          <router-link
            :to="cityRoute"
            class="back"
          >
            <i class="fa fa-angle-left mr-2" />
            {{ city.name }}
          </router-link>
        </div>

        <div
          v-if="enableFilter"
          class="filter-dropdown-menu"
        >
          <a
            class="btn-theme-picker"
            href="#"
            @click="showFilterMenu()"
          >
            <span>
              {{ __('Filter by') }}
              <i class="fa fa-filter ml-4" />
            </span>
          </a>

          <activity-filter
            v-if="showFilter"
            v-on-clickaway="hideFilter"
            :filters="filters"
            :price-range="priceRange"
            @filter="onChangeFilter($event)"
            @hide="hideFilter"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ActivityFilter from './components/ActivityFilter'
import {mixin as clickaway} from 'vue-clickaway'
import { route } from '../../mixins/route'
import { mapGetters } from 'vuex'

export default {
  components: {
    ActivityFilter
  },

  mixins: [
    clickaway,
    route
  ],

  props: {
    city: {
      type: Object,
      required: true
    },

    priceRange: {
      type: Object,
      required: true
    },

    filters: {
      type: Object,
      required: true
    },

    enableFilter: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      showFilter: false,
      filtersModel: {
        'duration': {
          title: 'Duration',
          items: [
            {
              name: '0 - 2 Hours'
            },
            {
              name: '2 - 4 Hours'
            },
            {
              name: '4 - 8+ Hours'
            }
          ]
        },
        'refundable': {
          title: 'Refundable',
          items: [
            {
              name: 'Fully-Refundable'
            },
            {
              name: 'Partly-Refundable'
            },
            {
              name: 'Non-Refundable'
            }
          ]
        },
        'pass_type': {
          //
        },
        'meal_included': {
          //
        }
      }
    }
  },

  computed: {
    ...mapGetters([
      'currentPlanId'
    ]),

    cityRoute () {
      return {
        name: `city`,
        params: {
          id: this.$route.params.cityId,
          index: this.$route.params.cityIndex,
          planId: this.currentPlanId
        },
        query: this.$route.query
      }
    }
  },

  methods: {
    onPriceChange (value) {
      this.$emit('price-changed', value)
    },

    onChangeFilter (filter) {
      this.hideFilter()

      this.$emit('filter', filter)
    },

    showFilterMenu () {
      this.showFilter = true
    },

    hideFilter () {
      this.showFilter = false
    }
  }
}
</script>
