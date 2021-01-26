<template>
  <div class="box box-border">
    <div class="itinerary travel">
      <div
        v-for="(route, index) in itinerary"
        :key="index"
        class="route active"
      >
        <div class="dot">
          <div class="city-code">
            <b>{{ route.city_code }}</b>
          </div>

          <i class="fa fa-plane icon" />
        </div>

        <div
          v-if="index !== itinerary.length - 1"
          class="route-line"
        >
          <p class="date-travel">
            {{ moment(itinerary[index + 1].date).format('ddd, DD MMM') }}
          </p>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
export default {
  props: {
    itinerary: {
      type: Array,
      required: true
    }
  }
}
</script>
