export const print = {
  methods: {
    initials (traveller) {
      if (traveller.first_name && traveller.last_name) {
        return `${traveller.first_name[0]}${traveller.last_name[0]}`
      }

      return traveller.reference_key
    },

    remainingTravellersCount (persons) {
      if (!persons.length) {
        return false
      }

      return persons.length - 2
    }
  }
}
