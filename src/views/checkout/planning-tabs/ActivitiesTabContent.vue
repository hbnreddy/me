<template>
  <div class="application">
    <calendar />

    <alert-popup
      v-if="showAlert"
      type="success"
      @hide="showAlert = false"
    >
      <div slot="alert">
        {{ message }}
      </div>
    </alert-popup>

    <add-to-plan-modal
      v-if="showPlanModal"
      :item="modifyActivity"
      :modify="true"
      @hide="showPlanModal = false"
      @updated="onItemUpdated()"
    />
  </div>
</template>

<script>
import AlertPopup from '../../../components/modals/alerts/AlertPopup'
import Calendar from '../../../components/calendar/Calendar'

import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import 'vue-cal/dist/vuecal.css'
import { route } from '../../../mixins/route'
import AddToPlanModal from '../../../components/modals/planning/AddToPlanModal'
import {ITEM_UPDATED_SUCCESSFULLY} from '../../../bootstrap/notify-messages'

export default {
  components: {
    AddToPlanModal,
    Calendar,
    AlertPopup
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      showPlanModal: false,
      modifyActivity: false,
      showAlert: false,
      message: ''
    }
  },

  computed: {
    ...mapGetters([
      'currentPlan'
    ])
  },

  methods: {
    onItemUpdated () {
      this.showPlanModal = false

      this.$notification.show(ITEM_UPDATED_SUCCESSFULLY)
    }
  }
}
</script>
