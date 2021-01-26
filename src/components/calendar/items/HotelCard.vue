<template>
  <div class="item-card hotel-card">
    <div class="item-body">
      <div class="top-side">
        <div class="mr-1 align-self-start">
          <i class="fa fa-bed icon" />
          <div class="title">
            {{ item.name }}
          </div>

          <div class="title font-weight-bold">
            Check in after 11:00
          </div>
        </div>

        <div class="vuecal__event-content d-flex">
          <div
            v-for="(person, personIndex) in computedTravellers"
            v-if="personIndex < 2"
            :key="personIndex"
            class="person"
          >
            {{ initials(person) }}
          </div>

          <div
            v-if="remainingTravellersCount(computedTravellers) > 0"
            class="person person-count"
          >
            +&nbsp;{{ remainingTravellersCount(item.travellers) }}
          </div>
        </div>
      </div>

      <div class="bottom-side">
        <div class="actions">
          <div
            v-if="item.status"
            :class="'in-progress'"
            class="status"
          >
            In progress
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { route } from '../../../mixins/route'
import {print} from '../../../mixins/print'

export default {
  mixins: [
    route,
    print
  ],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapGetters([
      'currentTravellers'
    ]),

    computedTravellers () {
      return this.currentTravellers.filter(e => {
        return this.item.travellers.includes(e.id)
      })
    }
  }
}
</script>
