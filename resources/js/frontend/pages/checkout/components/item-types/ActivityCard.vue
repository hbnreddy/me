<template>
  <div class="activity-card">
    <div
      class="d-flex justify-content-end semi-bold btn-x"
    >
      <a
        href="#"
        class="text-decoration-none"
        @click.prevent="popup = true"
      >
        X
      </a>

      <popup-menu
        v-if="popup"
        v-on-clickaway="hidePopup"
        :items="popupItems"
        @input="onInputPopup($event)"
      />
    </div>

    <div class="d-flex justify-content-between align-items-center semi-bold mb-2">
      <div class="vuecal__event-title text-left title">
        {{ item.name.substr(0, 24) + '..' }}
      </div>

      <div
        v-if="item.price"
        class="price"
      >
        {{ currency }}&nbsp;{{ Math.round(item.price) }}
      </div>
    </div>

    <div class="vuecal__event-content d-flex">
      <div
        v-for="(traveller, travellerIndex) in item.travellers"
        v-if="travellerIndex < 2"
        :key="travellerIndex"
        class="person"
      >
        {{ travellerKey(traveller) }}
      </div>

      <div
        v-if="remainingTravellers(item.travellers)"
        class="person person-count"
      >
        +&nbsp;{{ remainingTravellers(item.travellers) }}
      </div>
    </div>

    <add-to-plan-modal
      v-if="planModal"
      :id="item.item_id"
      :modify="true"
      :item="item"
      @updated="onUpdate()"
      @hide="planModal = false"
    />
  </div>
</template>

<script>
import PopupMenu from '../../../booking/components/PopupMenu'

import { query } from '../../../../mixins/query'
import {mixin as clickaway} from 'vue-clickaway'
import { mapGetters } from 'vuex'
import AddToPlanModal from '../../../../modals/AddToPlanModal'
import { route } from '../../../../mixins/route'

export default {
  components: {
    AddToPlanModal,
    PopupMenu
  },

  mixins: [
    clickaway,
    route,
    query
  ],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      popup: false,
      planModal: false,
      popupItems: [
        {
          text: 'Modify'
        },
        {
          text: 'Remove'
        }
      ]
    }
  },

  computed: {
    ...mapGetters([
      'travellers'
    ])
  },

  methods: {
    onUpdate () {
      this.planModal = false

      this.$redirect('', this.routeParams)
    },

    onInputPopup (index) {
      if (index === 0) {
        this.planModal = true
      } else if (index === 1) {
        this.$emit('remove')
      }

      this.hidePopup()
    },

    hidePopup () {
      this.popup = false
    },

    travellerKey (person) {
      const traveller = this.travellers.find(e => {
        return e.reference_key === person.reference_key
      })

      let attributes = false
      if (traveller) {
        attributes = traveller
      }

      if (attributes && attributes.first_name && attributes.last_name) {
        return attributes.first_name[0] + attributes.last_name[0]
      }

      return person.reference_key
    },

    remainingTravellers (travellers) {
      if (travellers.length < 2) {
        return false
      }

      return travellers.length - 2
    }
  }
}
</script>
