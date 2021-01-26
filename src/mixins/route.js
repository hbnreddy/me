import { mapGetters } from 'vuex'

export const route = {
    computed: {
        ...mapGetters([
            'query',
            'currentPlan'
        ]),

        //routeQuery () {
        // return !this.currentPlan && this.query
        //   ? {
        //     ...this.query,
        //     themes: this.query.themes.join(','),
        //     travellers: Object.values(this.query.travellers).join(',')
        //   }
        //   : {
        //     //
        //   }
        //}
        routeParams() {
            return {
                currency: this.query.currency.code.toLowerCase(),
                origin_city_id: this.query.origin_city.id,
                start_date: this.query.date.start_date,
                end_date: this.query.date.end_date,
                date_type: this.query.date.date_type,
                themes: this.query.themes.join('-'),
                travellers: Object.values(this.query.travellers).join('-')
            }
        }
    }
}