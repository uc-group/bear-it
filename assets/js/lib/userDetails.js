import { client, requestHandler } from '../api'

const cache = {};

const get = async (id) => {
  return JSON.parse(JSON.stringify(await cache[id]));
}

export const getUserDetails = async (id) => {
    if (!Object.hasOwnProperty.call(cache, id)) {
      cache[id] = client.post('/api/users', {
        users: [id]
      }).then(requestHandler)
        .then((result) => result[id])
    }

    return get(id)
}

export const getUserDetailList = (ids) => {
  const notCached = [];
  let result = {};
  ids.forEach((id) => {
    if (!Object.hasOwnProperty.call(cache, id)) {
      notCached.push(id);
    } else {
      result[id] = get(id)
    }
  })

  if (notCached.length) {
    const promiseResolves = {};
    const promiseRejects = {};
    notCached.forEach((id) => {
      cache[id] = new Promise((resolve, reject) => {
        promiseResolves[id] = resolve;
        promiseRejects[id] = reject;
      })
      result[id] = get(id)
    });

    client.post('/api/users', {
      users: notCached
    }).then(requestHandler)
      .then((users) => {
        Object.keys(users).forEach((userId) => {
          promiseResolves[userId](users[userId]);
          delete promiseRejects[userId];
        });

        Object.keys(promiseRejects).forEach((id) => {
          promiseRejects[id]();
          delete cache[id];
        });
      });
  }

  return result;
}

export default async (usernames) => {
  if (!Array.isArray(usernames)) {

  }
}
