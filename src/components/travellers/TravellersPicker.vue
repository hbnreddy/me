<template>
  <div class="dropdown-menu travellers show mt-1">
    <div class="dropdown-item">
      <div>{{ __('Adults') }}</div>

      <div class="actions">
        <a
          href="#"
          ref="adults_minus"
          class="text-primary border-primary disabled"
          @click.prevent="decreaseCount($const('ADULTS'))"
        >
          <span>&minus;</span>
        </a>

        <span class="semi-bold">{{ travellers.adults }}</span>

        <a
          href="#"
          ref="adults_add"
          class="text-primary border-primary"
          @click.prevent="increaseCount($const('ADULTS'))"
        >
          <span>&plus;</span>
        </a>
      </div>
    </div>

    <div class="dropdown-item">
      <div>
        <div>{{ __('Children') }}</div>
        <span class="small">{{ __('Age') }} 2-12</span>
      </div>

      <div class="actions">
        <a
          href="#"
          ref="children_minus"
          class="text-primary border-primary disabled"
          @click.prevent="decreaseCount($const('CHILDREN'))"
        >
          <span>&minus;</span>
        </a>
        <span class="semi-bold">{{ travellers.children }}</span>
        <a
          href="#"
          ref="children_add"
          class="text-primary border-primary"
          @click.prevent="increaseCount($const('CHILDREN'))"
        >
          <span>&plus;</span>
        </a>
      </div>
    </div>

    <div class="dropdown-item">
      <div>
        <div>{{ __('Infants') }}</div>
        <span class="small">{{ __('Age') }} 0-2</span>
      </div>

      <div class="actions">
        <a
          href="#"
          ref="infant_minus"
          data-toggle="tooltip" title="Hooray!"
          class="text-primary border-primary disabled"
          @click.prevent="decreaseCount($const('INFANTS'))"
        >
          <span>&minus;</span>
        </a>
        <span class="semi-bold">{{ travellers.infants }}</span>
        <a
          href="#"
          ref="infant_add"
          class="text-primary border-primary"
          @click.prevent="increaseCount($const('INFANTS'))"
        ><span>&plus;</span></a>
      </div>
    </div>

    <div v-if="errorMessage" class="dropdown-item">
      <p class="text-danger m-0">{{ errorMessage }}</p>
    </div>

    <div class="buttons text-right">
      <a
        href="#"
        class="mx-2 text-dark"
        @click.prevent="cancelTravellersModal"
      >{{ __('Cancel') }}</a>
      <a
        href="#"
        class="mx-2 text-primary"
        @click.prevent="applyTravellersCount"
      >{{ __('Apply') }}</a>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      errorMessage: false,
      travellers: {
        adults: 0,
        children: 0,
        infants: 0
      }
    }
  },

  watch: {
    value: {
      handler (value) {
        this.travellers = {
          ...value
        }
      },
      immediate: true
    }
  },

  methods: {
    applyTravellersCount () {
      this.errorMessage = false

      if (!this.travellers.adults) {
        this.errorMessage = this.__('Please select at least one adult.')

        return false
      }

      this.travellersModal = false

      this.$emit('input', {
        ...this.travellers
      })
      this.emitHide()
    },

    cancelTravellersModal () {
      this.travellersModal = false
      this.travellers = {
        ...this.value
      }

      this.emitHide()
    },

    increaseCount (type) {
      if(this.travellers.adults + this.travellers.children + this.travellers.infants < 9){
        this.travellers[type]++
        this.$refs.infant_minus.className= 'text-primary border-primary restore'
        this.$refs.adults_minus.className= 'text-primary border-primary restore'
        this.$refs.children_minus.className= 'text-primary border-primary restore'
      }
      else{
        this.$refs.infant_add.className= 'text-primary border-primary disabled'
        this.$refs.adults_add.className= 'text-primary border-primary disabled'
        this.$refs.children_add.className= 'text-primary border-primary disabled'
      }
    },

    decreaseCount (type) {
      this.travellers[type]--
      if (this.travellers[type] <= 0) {
        this.travellers[type] = 0
        if(this.travellers.adults + this.travellers.children + this.travellers.infants == 0){
          this.$refs.infant_minus.className= 'text-primary border-primary disabled'
          this.$refs.adults_minus.className= 'text-primary border-primary disabled'
          this.$refs.children_minus.className= 'text-primary border-primary disabled'
}
      }
      else{
        this.$refs.infant_add.className= 'text-primary border-primary restore'
          this.$refs.adults_add.className= 'text-primary border-primary restore'
          this.$refs.children_add.className= 'text-primary border-primary restore'
      }
    },

    emitHide () {
      this.$emit('hide')
    }
  }
}
</script>
<style type="text/css">
  .disabled {
  cursor: default;
  border-color: grey !important;
  color: grey !important;
}
.restore{
  cursor: pointer;
  color: #007bff !important;
}
</style>
