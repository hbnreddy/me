<template>
  <div class="order-statistics">
    <div class="wrapper">
      <table class="table orders-table">
        <thead>
          <tr>
            <th scope="col">
              #
            </th>

            <th scope="col">
              ORDER ID
            </th>

            <th scope="col">
              CUSTOMER
            </th>

            <th scope="col">
              DATE
            </th>

            <th scope="col">
              TOTAL
            </th>

            <th scope="col">
              STATUS
            </th>

            <th scope="col">

            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(order, index) in orders"
            :key="index"
          >
            <th scope="row">
              {{ index + 1 }}
            </th>

            <td scope="row">
              {{ order.identifier }}
            </td>

            <td>
              {{ order.customer.first_name }} {{ order.customer.last_name }}
            </td>

            <td>
              {{ order.date }}
            </td>

            <td>
              {{ order.currency }} {{ order.total_price }}
            </td>

            <td>
              {{ order.status }}
            </td>

            <td>
              <a
                href="#"
                class="btn-rut"
              >
                DETAILS
              </a>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="orders-sidebar">
        <section class="order-section">
          <p class="section-title">
            Total orders <small>({{ orders.length }})</small>
          </p>

          <div class="values">
            <div class="value pending">
              <i class="fa fa-spinner"></i>
              {{ pendingOrders.length }} Pending Orders
            </div>

            <div class="value refund">
              <i class="fa fa-exchange"></i>
              {{ refundedOrders.length }} Refunded Orders
            </div>

            <div class="value complete">
              <i class="fa fa-check"></i>
              {{ completedOrders.length }} Completed Orders
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data () {
    return {
      orders: []
    }
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

  created () {
    this.fetchOrders()
  },

  methods: {
    async fetchOrders () {
      await axios
        .get(`/api/orders`)
        .then(r => {
          if (r.data.success) {
            this.orders = r.data.entities
          }
        })
    }
  }
}
</script>
