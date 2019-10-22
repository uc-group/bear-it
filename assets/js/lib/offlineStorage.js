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

const optimizeEvents = function (events) {
    let optimizedEvents = events
    for (let name in optimizers) {
        optimizedEvents = optimizers[name](JSON.parse(JSON.stringify(optimizedEvents)))
    }

    return optimizedEvents
}

let handling = false;
const handleEvents = async function () {
    if (handling) {
        return;
    }

    handling = true;
    const events = optimizeEvents(eventStore)
    const time = Date.now();
    for (let i = 0; i < events.length; i++) {
        if (Date.now() - time > 1000) {
            break;
        }

        const event = events[i];
        if (!handlers[event.name]) {
            continue;
        }

        const result = await handlers[event.name](event)
        if (result === false) {
            continue;
        }

        bearItDb.db.remove(STORAGE_NAME, [event.id])
        const index = eventStore.findIndex(e => e.id === event.id)
        if (index >= 0) {
            eventStore.splice(index, 1)
        }
    }

    handling = false;
}
let handlingInterval = null;

export default (async function () {
    eventStore = await bearItDb.db.readAll(STORAGE_NAME);

    return {
        registerOptimizer(name, fn) {
            optimizers[name] = fn
        },
        registerHandler(name, fn) {
            handlers[name] = fn
        },
        put(event) {
            eventStore.push(event)

            return bearItDb.db.write(STORAGE_NAME, [JSON.parse(JSON.stringify(event))])
        },
        startHandling() {
            this.stopHandling();
            handlingInterval = setInterval(() => {
                handleEvents();
            }, 2000)
        },
        stopHandling() {
            clearInterval(handlingInterval);
            handlingInterval = null;
        }
    }
})()

