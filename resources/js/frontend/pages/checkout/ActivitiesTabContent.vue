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

    <add-place-to-plan-modal
      v-if="showModifyPlaceModal"
      :item="modifyActivity"
      :modify="true"
      @hide="hideAddToPlanModals"
      @updated="onItemUpdated()"
    />

    <add-experience-to-plan-modal
      v-if="showModifyExperienceModal"
      :item="modifyActivity"
      :modify="true"
      @hide="hideAddToPlanModals"
      @updated="onItemUpdated()"
    />
  </div>
</template>

<script>
import AlertPopup from '../../modals/AlertPopup'
import AddPlaceToPlanModal from '../../modals/AddPlaceToPlanModal'
import AddExperienceToPlanModal from '../../modals/AddExperienceToPlanModal'
import Calendar from './components/Calendar'

import * as moment from 'moment'
import {mapGetters} from 'vuex'
import {mixin as clickaway} from 'vue-clickaway'
import 'vue-cal/dist/vuecal.css'
import {chunk} from 'lodash'
import axios from 'axios'
import { route } from '../../mixins/route'

export default {
  components: {
    Calendar,
    AddPlaceToPlanModal,
    AddExperienceToPlanModal,
    AlertPopup
  },

  mixins: [
    clickaway,
    route
  ],

  data () {
    return {
      showModifyPlaceModal: false,
      showModifyExperienceModal: false,
      modifyActivity: false,
      showAlert: false,
      message: '',
      items: {
        //
      },
      hourLines: [],
      showCancelActivityPopup: false,
      menuItems: [
        {
          text: 'Modify',
          handler: (transport) => {
            this.activityPopupMenu[transport.id] = false

            let key = 'place_id'
            if (transport.type === 'activity') {
              this.showModifyExperienceModal = true
              key = 'musement_id'
            } else {
              this.showModifyPlaceModal = true
            }

            const item = this.items[transport.date].find(e => {
              return e.attributes[key] === transport.id
            })

            this.modifyActivity = {
              id: transport.cartId,
              name: item.attributes.title,
              type: transport.type,
              uuid: item.attributes.musement_id,
              price: item.attributes.price,
              timeslot: item.timeslot,
              travellers: item.travellers
            }
          }
        },
        {
          text: 'Remove',
          handler: (transport) => {
            this.removeItem(transport.id, transport.type, transport.date)
            this.activityPopupMenu[transport.id] = false
          }
        }
      ],
      activityPopupMenu: {
      },
      activeTab: 1,
      selectedChunk: 0,
      headings: []
    }
  },

  computed: {
    ...mapGetters([
      'loading',
      'query',
      'currentPlanId',
      'plans',
      'cart'
    ]),

    currentPlanObject () {
      return this.plans.find(e => {
        return e.id === this.currentPlan
      })
    },

    chunkedHeadings () {
      if (!this.headings.length) {
        return []
      }

      return chunk(this.headings, 3)
    }
  },

  methods: {
    hideAddToPlanModals () {
      this.showModifyPlaceModal = false
      this.showModifyExperienceModal = false
    },

    onItemUpdated () {
      this.showModifyPlaceModal = false
      this.showModifyExperienceModal
      this.message = 'Item has been updated successfully'
      this.showAlert = true

      setTimeout(() => {
        this.$redirect('', this.routeParams)
      }, 1500)
    },

    async processHeadingsCities () {
      const headings = this.headings

      for (let i = 0; i < headings.length; ++i) {
        headings[i] = {
          ...headings[i],
          city: this.items[headings[i].date] && this.items[headings[i].date].length
            ? await (this.getCityName(this.items[headings[i].date][0].attributes.city_id))
            : false
        }
      }

      this.headings = [
        ...headings
      ]
    },

    async getCityName (id) {
      return await axios
        .get(`/api/cities/${id}/name`)
        .then(e => {
          return e.data.entity
        })
    },

    async init () {
      this.processDays()
    },

    async fetchPlans () {
      await axios
        .get(`/api/cart/plans`)
        .then(r => {
          this.$store.commit('setPlans', r.data.entities)
        })
    },

    async removeItem (item_id, type, date) {
      const success = await this.$store.dispatch('removeItemFromCart', {
        plan_id: this.currentPlan,
        item_id,
        type
      })

      if (success) {
        this.message = 'Item removed successfully!'
        this.showAlert = true

        const index = this.items[date].findIndex(e => {
          return e.id === item_id
        })

        this.items[date].splice(index, 1)

        setTimeout(() => {
          this.$redirect('', this.routeParams)
        }, 1500)
      }
    },

    async processItemsPerDate () {
      for (const e of this.headings) {
        this.items = {
          ...this.items,
          [e.date]: await this.processItems(e.date)
        }
      }
    },

    async processItems (date) {
      if (!this.currentPlan) {
        return []
      }

      let items = []

      await axios
        .get(`/api/cart/plans/${this.currentPlanId}/items`, {
          params: {
            date
          }
        })
        .then(r => {
          items = r.data.entities.map(e => {
            return {
              ...e,
              travellers: this.processTravellers(e.travellers)
            }
          })
        })

      return items
    },

    processPrice (prices) {
      return Object.values(prices).reduce((sum, x) => {
        return sum + x
      })
    },

    processTravellers (travellers) {
      let results = []

      const planTravellers = this.currentPlanObject.travellers

      let index = 0
      while (index < travellers.adults.length) {
        if (travellers.adults[index].attributes.active) {
          if (planTravellers.adults[index].attributes.key) {
            results.push(planTravellers.adults[index].attributes.key)
          } else {
            results.push(`A${index + 1}`)
          }
        }

        index++
      }

      index = 0
      while (index < travellers.children.length) {
        if (travellers.children[index].attributes.active) {
          if (planTravellers.children[index].attributes.key) {
            results.push(planTravellers.children[index].attributes.key)
          } else {
            results.push(`C${index + 1}`)
          }
        }

        index++
      }

      index = 0
      while (index < travellers.infants.length) {
        if (travellers.infants[index].attributes.active) {
          if (planTravellers.infants[index].attributes.key) {
            results.push(planTravellers.infants[index].attributes.key)
          } else {
            results.push(`I${index + 1}`)
          }
        }

        index++
      }

      return results
    },

    processDays () {
      this.headings = []
      const date = this.query.date

      let a = moment(date.start_date)
      const b = moment(date.end_date)

      let diff = b.diff(a, 'days') + 1

      while (diff) {
        this.headings.push({
          city: false,
          date: a.format(this.$const('BASE_DATE_FORMAT'))
        })

        a.add(1, 'days')

        diff--
      }
    },

    hideAllPopupMenus () {
      const items = this.activityPopupMenu

      Object.keys(items)
        .forEach(key => {
          this.activityPopupMenu = {
            ...this.activityPopupMenu,
            [key]: false
          }
        })
    },

    hidePopupMenu (index) {
      this.activityPopupMenu[index] = this.activityPopupMenu[index] ? this.activityPopupMenu[index] : false

      this.activityPopupMenu = {
        ...this.activityPopupMenu,
        [index]: false
      }
    },

    showActivityPopupMenu (index) {
      this.activityPopupMenu[index] = this.activityPopupMenu[index] ? this.activityPopupMenu[index] : false

      this.activityPopupMenu = {
        ...this.activityPopupMenu,
        [index]: true
      }
    },

    hideActivityPopupMenu (index) {
      this.activityPopupMenu[index] = this.activityPopupMenu[index] ? this.activityPopupMenu[index] : false

      this.activityPopupMenu = {
        ...this.activityPopupMenu,
        [index]: false
      }
    },

    setActiveTab (tabIndex) {
      this.activeTab = tabIndex
    },

    translateHour (i) {
      return i > 9
        ? `${i}:00`
        : `0${i}:00`
    },

    todayHasActivities (item) {
      return !!item.activities
    },

    getPersonInitials (person) {
      return person
    },

    getRemainingPersons (persons) {
      if (!persons.length) {
        return false
      }

      return persons.length - 2
    },

    showPrev (index) {
      this.selectedChunk = index ? index - 1 : 0

      if (this.items.length) {
        this.showCurrentChunk()
      }
    },

    showNext (index) {
      if ((this.selectedChunk + 1) === this.chunkedHeadings.length) {
        return false
      }

      this.selectedChunk = index + 1

      if (this.items.length) {
        this.showCurrentChunk()
      }
    },

    showCurrentChunk () {
      document.querySelectorAll('.chunk').forEach(e => {
        e.style.display = 'none'
      })
      document.querySelector(`.chunk.chunk-${this.selectedChunk}`).style.display = 'flex'
    },

    goToExplore () {
      this.$redirect('explore', this.routeParams)
    }
  }
}
</script>
