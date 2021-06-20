(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['idb'], factory)
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('idb'))
    } else {
        root.bearItDb = factory(root.idb)
    }
}(typeof self !== 'undefined' ? self : this, function(idb) {
    const Stores = {
        PROJECTS: 'projects',
        KEYVAL: 'keyval',
        OFFLINE_STORAGE: 'offline-store'
    }

    const dbPromise = idb.openDB('bear-it-store', 1, {
        upgrade(db) {
            if (!db.objectStoreNames.contains(Stores.PROJECTS)) {
                const store = db.createObjectStore(Stores.PROJECTS, {keyPath: 'id'})
                store.createIndex('version', 'version')
            }
            if (!db.objectStoreNames.contains(Stores.KEYVAL)) {
                db.createObjectStore(Stores.KEYVAL)
            }
            if (!db.objectStoreNames.contains(Stores.OFFLINE_STORAGE)) {
                db.createObjectStore(Stores.OFFLINE_STORAGE, {keyPath: 'id'})
            }
        }
    });

    function write(storeName, data) {
        return dbPromise.then(db => {
            const tx = db.transaction(storeName, 'readwrite')
            const store = tx.objectStore(storeName)
            data.forEach(row => {
                store.put(row)
            })
            return tx.complete
        })
    }

    function remove(storeName, ids) {
        return dbPromise.then(db => {
            const tx = db.transaction(storeName, 'readwrite')
            const store = tx.objectStore(storeName)
            ids.forEach(id => {
                store.delete(id)
            })
            return tx.complete
        })
    }

    function readAll(storeName) {
        return dbPromise.then(db => db.getAll(storeName))
    }

    return {
        db: {
            write,
            remove,
            readAll,
            find(storeName, key) {
                return dbPromise.get(storeName, key)
            }
        },
        updateProjectList: function (currentList) {
            return dbPromise.then(db => {
                return db.clear(Stores.PROJECTS)
            }).then(() => {
                return write(Stores.PROJECTS, currentList)
            })
        },
        getProjectList: function () {
            return readAll(Stores.PROJECTS).then(list => list.sort((a, b) => a.name.toLocaleLowerCase() < b.name.toLowerCase() ? -1 : 1))
        },
        removeProject: function(id) {
            return remove(Stores.PROJECTS, [id])
        },
        keyval: {
            async get(key) {
                return (await dbPromise).get('keyval', key);
            },
            async set(key, val) {
                return (await dbPromise).put('keyval', val, key);
            },
            async delete(key) {
                return (await dbPromise).delete('keyval', key);
            },
            async clear() {
                return (await dbPromise).clear('keyval');
            },
            async keys() {
                return (await dbPromise).getAllKeys('keyval');
            },
        }
    }
}))
