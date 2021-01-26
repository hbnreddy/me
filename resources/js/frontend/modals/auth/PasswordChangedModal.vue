<template>
  <modal
    :show-close="false"
    class="modal-login"
  >
    <h5
      slot="modal-title"
      class="modal-title"
    >
      <span>{{ __('Password updated') }} !</span>
    </h5>

    <div
      slot="modal-body"
      class="modal-body-content"
    >
      <div class="text">
        {{ __('Your password has been changed successfully') }} !
      </div>
    </div>

    <div slot="modal-footer" />
  </modal>
</template>

<script>
import Modal from '../../../components/Modal'

export default {
  components: {
    Modal
  }
}
</script>
