import { mapState, mapActions } from 'vuex';
import offlineStorage, { OfflineEvent } from '../lib/offlineStorage'

export default {
    computed: {
        ...mapState(['offline'])
    },
    methods: {
        ...mapActions(['checkOffline']),
        async putOfflineEvent(name, payload) {
            (await offlineStorage).put(new OfflineEvent(name, payload))
        }
    }
}
