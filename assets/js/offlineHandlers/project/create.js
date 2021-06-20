import offlineStorage, { OfflineEvent } from '../../lib/offlineStorage'
import api from '@api/project'

offlineStorage.then(eventStore => {
    eventStore.registerHandler('project_create', async (event) => {
        const {id, name, description, color} = event.payload

        try {
            await api.create(id, name, description, color)
        } catch(error) {
            if (!error.response || !error.response.data || error.response.data.status !== 'ERROR_VALIDATION') {
                return false;
            }

            const data = error.response.data;
            const payload = event.payload;
            payload.errors = data.errors;
            eventStore.put(new OfflineEvent('project_create_sync_error', payload));

            return true;
        }
    });
})
