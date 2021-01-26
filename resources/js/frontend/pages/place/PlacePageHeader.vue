<template>
  <div class="planning">
    <div class="container-fluid">
      <div class="planning-content place-of-interest">
        <div class="title">
          <a
            href="#"
            class="back"
            @click.prevent="goBackToCity()"
          >
            <i class="fa fa-angle-left mr-2" />
            {{ city.name }}
          </a>
        </div>

        <div
          v-if="enableFilter"
          class="filter-dropdown-menu blob blue"
        >
          <a
            class="btn-theme-picker blob blue"
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

  methods: {
    onChangeFilter (filter) {
      this.hideFilter()

      this.$emit('filter', filter)
    },

    showFilterMenu () {
      this.showFilter = true
    },

    hideFilter () {
      this.showFilter = false
    },

    goBackToCity () {
      this.$redirect('explore', this.routeParams, `/city/${this.city.id}`)
    }
  }
}
</script>
