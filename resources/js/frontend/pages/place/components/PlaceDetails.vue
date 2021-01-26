<template>
  <div class="place-details">
    <div class="poi-description">
      <span v-if="fullDescription">
        {{ entity.description }}
      </span>

      <span v-else>
        {{ shortText(entity.description) }}
      </span>

      <a
        v-if="fullDescription && entity.description && entity.description.length > 300"
        href="#"
        class="text-decoration-none"
        @click.prevent="fullDescription = false"
      >{{ __('Show less') }}
      </a>

      <a
        v-else-if="!fullDescription && entity.description && entity.description.length > 300"
        href="#"
        class="text-decoration-none"
        @click.prevent="fullDescription = true"
      >{{ __('Show more') }}
      </a>
    </div>

    <div class="poi-description">
      <i class="fa fa-map-marker icon text-primary mr-2" />
      {{ entity.name_suffix }}
    </div>
  </div>
</template>

<script>
export default {
  props: {
    entity: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      fullDescription: false
    }
  },

  methods: {
    shortText (text) {
      if (!text) {
        return ''
      }

      return text.substr(0, 300) + '..'
    }
  }
}
</script>
