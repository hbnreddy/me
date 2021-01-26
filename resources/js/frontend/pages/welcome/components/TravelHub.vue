<template>
  <div class="travel-hub-wrapper">
    <div class="container">
      <h2 class="category-title">
        {{ __('Easy to use. Easy to browse') }}
      </h2>

      <div
        v-for="(hub, index) in hubs"
        :key="index"
        :data-order="index + 1"
        :class="{ 'flex-row-reverse' : index % 2 }"
        class="row line"
      >
        <div class="col-md-6">
          <div class="column">
            <img
              :src='`/images/landingpage/${hub.image}`'
              class="img-fluid hub-image"
              alt=""
            />
          </div>
        </div>

        <div class="col-md-6">
          <div class="column">
            <div class="hub-title">
              {{ hub.title }}
            </div>

            <div class="hub-subtitle">
              {{ hub.subtitle }}
            </div>

            <div class="hub-subtitle">
              {{ hub.text }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  props: {
    hubs: {
      type: Array,
      default: () => [
        //
      ]
    }
  },
}

</script>
