<template>
  <div class="side-column">
    <p class="column-title">
      <span class="btn-rut">
        ADMIN PANEL
      </span>
    </p>

    <nav class="nav flex-column">
      <div class="wrapper">
        <div
          v-for="(item, itemIndex) in items"
          :key="itemIndex"
          class="list"
        >
          <div class="title">{{ item.title }}</div>

          <ul class="sidebar-list">
            <li
              v-for="(listItem, listItemIndex) in item.list"
              :key="listItemIndex"
              class="sidebar-item"
            >
              <router-link
                :to="listItem.url"
                :class="{ 'active': activeUrl === listItem.url }"
                class="nav-link"
                href="#"
              >
                <i
                  v-if="activeUrl === listItem.url"
                  class="fa fa-angle-right active-dot"
                />

                <i
                  :class="'fa-' + listItem.icon"
                  class="fa"
                />
                {{ listItem.text }}
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
</template>

<script>
export default {
  data () {
    return {
      toggle: true,
      items: [
        {
          title: 'Statistics',
          list: [
            {
              text: 'Dashboard',
              icon: 'cubes',
              url: '/'
            }
          ]
        },
        {
          title: 'Manage',
          list: [
            {
              text: 'Orders',
              icon: 'list-alt',
              url: '/orders'
            },
            {
              text: 'Media',
              icon: 'picture-o',
              url: '/media-management'
            },
            {
              text: 'City Weather',
              icon: 'cloud',
              url: '/weather'
            },
            {
              text: 'Language',
              icon: 'language',
              url: '/language-management'
            }
          ]
        },
        {
          title: 'Organize',
          list: [
            {
              text: 'Continents',
              icon: 'globe',
              url: '/continents'
            },
            {
              text: 'Countries',
              icon: 'paper-plane',
              url: '/countries'
            },
            {
              text: 'Cities',
              icon: 'location-arrow',
              url: '/cities'
            },
            {
              text: 'Themes',
              icon: 'edit',
              url: '/themes'
            },
            {
              text: 'Places of Interest',
              icon: 'map-marker',
              url: '/places-of-interest'
            },
            {
              text: 'Markers',
              icon: 'thumb-tack',
              url: '/markers'
            },
            {
              text: 'Venues',
              icon: 'calendar',
              url: '/venues'
            }
          ]
        },
        {
          title: 'Settings',
          list: [
            {
              text: 'Access',
              icon: 'key',
              url: '/access-management'
            },
            {
              text: 'Settings',
              icon: 'cog',
              url: '/settings'
            }
          ]
        }
      ],
      activeUrl: false
    }
  },

  watch: {
    $route: {
      handler (route) {
        this.activeUrl = route.path
      },
      immediate: true
    }
  },

  methods: {
    onClickItem (url) {
      this.activeUrl = url
    }
  }
}
</script>
