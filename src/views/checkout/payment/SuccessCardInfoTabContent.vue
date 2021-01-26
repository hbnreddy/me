<template>
  <div class="card-info-success py-4">
    <div class="text-muted h5 mb-4">
      Thank you!
    </div>

    <form class="form mb-4">
      <div class="custom-control custom-checkbox cursor-pointer">
        <input
          id="terms-agree"
          :value="agreement"
          type="checkbox"
          class="custom-control-input"
        >

        <label
          for="terms-agree"
          class="custom-control-label text-muted"
        >
          I agree with Terms and Conditions
        </label>
      </div>
    </form>

    <div class="alert alert-danger">
      Errors!
    </div>

    <div class="d-flex justify-content-end pb-4">
      <button
        class="btn btn-danger w-25 m-1"
        @click.prevent="hide($event)"
      >
        <i class="fa fa-close icon" />
        {{ __('Cancel') }}
      </button>

      <button
        class="btn btn-warning m-1"
        @click.prevent="submit($event)"
      >
        <i class="fa fa-check icon" />
        {{ __('Start Booking') }}
      </button>
    </div>
  </div>
</template>

<script>

export default {
  data () {
    return {
      agreement: ''
    }
  },

  computed: {
    //
  },

  created () {
    //
  },

  methods: {
    hide () {
      this.$emit('hide')
    },

    submit () {
      this.$emit('submit')
    }
  }
}
</script>
