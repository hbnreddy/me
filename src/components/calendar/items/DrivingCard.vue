<template>
  <div class="vuecal__event-content d-flex align-items-center driving-card">
    <div class="dot-line">
      <i class="fa fa-map-marker icon" />
    </div>

    <i class="fa fa-car icon mr-2" />

    <span class="title">
      {{ getEventDuration(item) }} h
    </span>

    <i class="fa fa-angle-right mx-3 arrow" />
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import { route } from '../../../mixins/route'

export default {
  mixins: [
    route
  ],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

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

  created () {
    for (let i = 6; i < 24; i++) {
      if (i < 10) {
        this.hourLines.push(`0${i}`)
      } else {
        this.hourLines.push(`${i}`)
      }
    }

    this.hourLines.push(`00`)

    this.init()
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
      await this.processDays()
      await this.processItemsPerDate()
      await this.processHeadingsCities()
    },

    async removeItem (item_id, type, date) {
      const success = await this.$store.dispatch('removeItemFromCart', {
        planId: this.currentPlan,
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
        .get(`/cart/plans/${this.currentPlanId}/items`, {
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
      return Object.values(prices)
        .reduce((sum, x) => {
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

    async processDays () {
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

    getEventDuration (event) {
      const timeslot = event.timeslot

      if ((timeslot.duration / 60) > 12) {
        return 1
      }

      if (!event.timeslot.duration) {
        return parseInt(timeslot.end_time) - parseInt(timeslot.start_time)
      }

      return parseInt(timeslot.duration / 60)
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
      document.querySelectorAll('.chunk')
        .forEach(e => {
          e.style.display = 'none'
        })
      document.querySelector(`.chunk.chunk-${this.selectedChunk}`).style.display = 'flex'
    },

    eventHeight (event) {
      let time = parseInt(event.timeslot.start_time)

      if (time === 0) {
        time = 24
      }

      time -= 6

      if (time < 0) {
        return false
      }

      return {
        top: 'calc(90px * ' + time + ')',
        height: 'calc(90px * ' + this.getEventDuration(event) + ')'
      }
    },

    goToExplore () {
      this.$redirect('explore', this.routeParams)
    }
  }
}
</script>
