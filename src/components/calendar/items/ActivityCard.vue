<template>
  <div class="activity-card item-card">
    <div
      class="d-flex justify-content-end semi-bold btn-x"
    >
      <a
        href="#"
        class="text-decoration-none btn-close"
        @click.prevent="popup = true"
      >
        <i class="fa fa-times" />
      </a>

      <popup-menu
        v-if="popup"
        v-on-clickaway="hidePopup"
        :items="popupItems"
        @input="onInputPopup($event)"
      />
    </div>

    <div class="item-body">
      <div class="top-side">
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
            v-for="(traveller, travellerIndex) in computedTravellers"
            v-if="travellerIndex < 2"
            :key="travellerIndex"
            class="person"
          >
            {{ initials(traveller) }}
          </div>

          <div
            v-if="remainingTravellers(computedTravellers)"
            class="person person-count"
          >
            +&nbsp;{{ remainingTravellers(computedTravellers) }}
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
import PopupMenu from '../../popups/PopupMenu'
import AddToPlanModal from '../../../components/modals/planning/AddToPlanModal'

import { query } from '../../../mixins/query'
import {mixin as clickaway} from 'vue-clickaway'
import { mapGetters } from 'vuex'
import { route } from '../../../mixins/route'
import {print} from '../../../mixins/print'
import {ITEM_UPDATED_SUCCESSFULLY} from '../../../bootstrap/notify-messages'

export default {
  components: {
    AddToPlanModal,
    PopupMenu
  },

  mixins: [
    clickaway,
    route,
    query,
    print
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
      'currentTravellers'
    ]),

    computedTravellers () {
      return this.currentTravellers.filter(e => {
        return this.item.travellers.includes(e.id)
      })
    }
  },

  methods: {
    onUpdate () {
      this.planModal = false

      this.$notification.show(ITEM_UPDATED_SUCCESSFULLY)
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

    remainingTravellers (travellers) {
      if (travellers.length < 2) {
        return false
      }

      return travellers.length - 2
    }
  }
}
</script>
