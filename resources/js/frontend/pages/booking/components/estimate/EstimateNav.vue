<template>
  <nav class="plan-filters">
    <div
      id="nav-tab"
      class="nav nav-pills"
      role="tablist"
    >
      <a
        id="nav-summary-tab"
        class="nav-item nav-link active"
        data-toggle="tab"
        href="#nav-summary"
        role="tab"
        aria-controls="nav-summary"
        aria-selected="true"
      >
        {{ __('All') }}
      </a>

      <a
        id="nav-people-tab"
        class="nav-item nav-link"
        data-toggle="tab"
        href="#nav-people"
        role="tab"
        aria-controls="nav-people"
        aria-selected="false"
      >
        <i class="fa fa-users" />
      </a>
    </div>

    <div class="more-options">
      <i class="fa fa-ellipsis-h" />
    </div>
  </nav>
</template>

<script>
export default {
  data () {
    return {
      planner: [
        {
          city: 'Paris',
          activities: [
            {
              title: 'Eiffel tower climing',
              people: 5,
              price: 350
            },
            {
              title: 'Food tasting',
              people: 4,
              price: 350
            },
            {
              title: 'River rafting',
              people: 2,
              price: 350
            }
          ]
        },
        {
          city: 'Barcelona',
          activities: [
            {
              title: 'Eiffel tower climing',
              people: 5,
              price: 350
            },
            {
              title: 'Food tasting',
              people: 4,
              price: 350
            },
            {
              title: 'River rafting',
              people: 2,
              price: 350
            }
          ]
        }
      ],
      totalEstimation: 1020
    }
  },

  methods: {
    calculateTotalEstimation () {
      return this.totalEstimation
    }
  }
}
</script>
