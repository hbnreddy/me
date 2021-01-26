<template>
  <div class="order-page">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center my-2">
        <h3 class="my-4">
          Order 1
        </h3>

        <router-link
          to="/orders"
          class="btn-rut"
        >
          <i class="fa fa-angle-left mr-1" />
          Back to Orders
        </router-link>
      </div>

      <div class="order-details my-2">
        <div class="box">
          <p class="title">
            Main customer
          </p>
        </div>

        <div class="box">
          <p class="title">
            Participants
          </p>
        </div>

        <div class="box">
          <p class="title">
            Payment
          </p>
        </div>

        <div class="box">
          <p class="title">
            Musement References
          </p>
        </div>

        <div class="box">
          <p class="title">
            Products
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      order: {
        customer: {
          first_name: 'Laurentiu',
          last_name: 'Dodoloi'
        },
        identifier: 'R-13F24GG4F23FFF243IKGM54IUG',
        total_price: 344,
        currency: '$',
        status: 'PENDING',
        date: '14-07-1997'
      }
    }
  },

  created () {
    this.fetchOrders()
  },

  computed: {
    pendingOrders () {
      return this.orders.filter(e => {
        return e.status === 'PENDING'
      })
    },

    refundedOrders () {
      return this.orders.filter(e => {
        return e.status === 'REFUNDED'
      })
    },

    completedOrders () {
      return this.orders.filter(e => {
        return e.status === 'OK'
      })
    }
  },

  methods: {
    async fetchOrders () {
      await axios
        .get(`/api/orders`)
        .then(r => {
          if (r.data.success) {
            //
          }
        })
    }
  }
}
</script>
