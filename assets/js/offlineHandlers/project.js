import offlineStorage from '../lib/offlineStorage'
import api from '@api/project'

offlineStorage.then(eventStore => {
    eventStore.registerHandler('project_create', async (event) => {
        const {id, name, description} = event.payload

        return api.create(id, name, description)
    });
})
