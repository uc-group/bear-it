import bearItDb from '@publiclib/db-utils';
import uuid from 'uuid/v4';

const STORAGE_NAME = 'offline-store';

let eventStore = [];
const optimizers = {};
const handlers = {};

export class OfflineEvent {
    constructor(name, payload) {
        this.id = uuid();
        this.name = name;
        this.payload = payload;
        this.date = Date.now();
    }
}

class Storage {
    constructor() {
        this.handling = false
        this.handlingInterval = null
        this.handlers = []
        this.optimizers = []
        this.listeners = {
            put: [],
            remove: [],
            beforeHandle: [],
            afterHandle: []
        }
    }

    optimizeEvents(events) {
        let optimizedEvents = events
        for (let name in optimizers) {
            optimizedEvents = optimizers[name](JSON.parse(JSON.stringify(optimizedEvents)))
        }

        return optimizedEvents
    }

    startHandling() {
        this.handlingInterval = setInterval(async () => {
            await this.handleEvents();
        }, 2000)
    }

    stopHandling() {
        clearInterval(this.handlingInterval);
        this.handlingInterval = null;
    }

    async handleEvents() {
        if (this.handling) {
            return;
        }

        const events = this.optimizeEvents(eventStore)
        const time = Date.now();
        for (let i = 0; i < events.length; i++) {
            if (Date.now() - time > 1000) {
                break;
            }

            const event = events[i];
            if (!this.handlers[event.name]) {
                continue;
            }

            this.notify('beforeHandle', [event])
            const result = await this.handlers[event.name](event)
            this.notify('afterHandle', [event, result])
            if (result === false) {
                continue;
            }

            bearItDb.db.remove(STORAGE_NAME, [event.id])
            this.notify('remove', [event])
            const index = eventStore.findIndex(e => e.id === event.id)
            if (index >= 0) {
                eventStore.splice(index, 1)
            }
        }

        this.handling = false;
    }

    notify(name, args) {
        this.listeners[name].forEach(listener => {
            listener(...args)
        })
    }

    addListener(name, fn) {
        this.listeners[name].push(fn);
    }

    removeListener(name, fn) {
        const index = this.listeners[name].findIndex(listener => listener === fn);
        if (index >= 0) {
            this.listeners[name].splice(index, 1)
        }
    }
}

export default (async function () {
    eventStore = await bearItDb.db.readAll(STORAGE_NAME);
    const storage = new Storage();

    return {
        registerOptimizer(name, fn) {
            storage.optimizers[name] = fn
        },
        registerHandler(name, fn) {
            storage.handlers[name] = fn
        },
        put(event) {
            eventStore.push(event)
            storage.notify('put', [event])
            return bearItDb.db.write(STORAGE_NAME, [JSON.parse(JSON.stringify(event))])
        },
        startHandling: storage.startHandling.bind(storage),
        stopHandling: storage.stopHandling.bind(storage),
        addListener: storage.addListener.bind(storage),
        removeListener: storage.removeListener.bind(storage),
        forEachEvent(fn) {
            eventStore.forEach(event => {
                fn(event)
            })
        }
    }
})()

