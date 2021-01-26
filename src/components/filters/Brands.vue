<template>
  <div
    v-on-clickaway="closeFilterComponent"
    class="dropdown"
  >
    <a
      class="btn filter-item"
      @click="toggleFilterComponent"
    >
      <span v-if="appliedFilter">{{ appliedFilter }}</span>
      <span v-else>Brands</span>
    </a>

    <div
      v-if="filterComponent"
      class="dropdown-menu shadow-sm"
    >
      <div class="heading">
        <div class="filter-name">
          {{ __('Brands') }}
        </div>

        <div class="small text-muted">
          5 {{ __('locations') }}
        </div>
      </div>

      <div class="body">
        <div
          v-for="(brand, brandIndex) in hotelBrand"
          :key="brandIndex"
          class="form-group custom-control custom-checkbox"
        >
          <div class="collapse-header">
            <input
              :id="'brand-' + brandIndex"
              type="checkbox"
              class="custom-control-input"
            >
            <label
              :for="'brand-' + brandIndex"
              class="custom-control-label font-weight-normal"
            >
              {{ brand.brand }}
            </label>

            <i
              v-if="brand.hotels"
              class="fa fa-angle-down p-1 toggle-collapse"
              :data-target="'#collapse' + brandIndex"
              :aria-controls="'collapse' + brandIndex"
              data-toggle="collapse"
              aria-expanded="false"
            />
          </div>

          <div
            :id="'collapse' + brandIndex"
            class="collapse"
          >
            <div
              v-for="(hotel, index) in brand.hotels"
              :key="index"
              class="form-group custom-control custom-checkbox"
            >
              <input
                :id="'hotel-' + brandIndex + index"
                type="checkbox"
                class="custom-control-input"
              >
              <label
                :for="'hotel-' + brandIndex + index"
                class="custom-control-label font-weight-normal"
              >
                {{ hotel.name }}
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="buttons text-right p-2">
        <a
          href="#"
          class="mx-2 text-dark text-decoration-none"
          @click.prevent="closeFilterComponent()"
        >{{ __('Cancel') }}</a>
        <a
          href="#"
          class="mx-2 text-primary text-decoration-none"
          @click.prevent="applyFilter()"
        >{{ __('Apply') }}</a>
      </div>
    </div>
  </div>
</template>

<script>
import {mixin as clickaway} from 'vue-clickaway'

export default {
  mixins: [
    clickaway
  ],

  props: {
    value: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      filterComponent: false,
      appliedFilter: '',
      hotelBrand: [
        {
          brand: 'Best Western International',
          hotels: [
            {
              name: 'Best Western'
            },
            {
              name: 'Best Western Plus'
            },
            {
              name: 'Best Western Premier'
            }
          ]
        },
        {
          brand: 'Four Seasons'
        },
        {
          brand: 'Best Western International',
          hotels: [
            {
              name: 'Best Western'
            },
            {
              name: 'Best Western Plus'
            },
            {
              name: 'Best Western Premier'
            }
          ]
        },
        {
          brand: 'Best Western International',
          hotels: [
            {
              name: 'Best Western'
            },
            {
              name: 'Best Western Plus'
            },
            {
              name: 'Best Western Premier'
            }
          ]
        },
        {
          brand: 'Four Seasons'
        }
      ]
    }
  },

  methods: {
    onChange () {

    },

    applyFilter () {
      this.filterComponent = false
    },

    toggleFilterComponent () {
      this.filterComponent = !this.filterComponent
    },

    closeFilterComponent () {
      this.filterComponent = false
    }
  }
}
</script>
