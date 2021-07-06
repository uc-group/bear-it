# Websocket rooms

Websocket rooms are concept of an entity having attached systems 
what adds behaviour handling user connections.

For example chat system is attached to rooms what name starts with `chat/`.
That system will handle sending messages to it 
and also is sending messages to the client.

You can read how the systems and rooms exactly works on the server side [here](https://github.com/uc-group/bear-it-ws-server/blob/master/docs/index.md).

From the client side we just need to know what room we want to connect to and what messages should be handled.
To make it easier there are Vue components you can use.

Example: We want to join chat room `chat/awesome-app`.

```html
<ws-room :ref="wsRoom"
         name="chat/awesome-app"
         @connected="onConnected"
         @disconnected="onDisconnected"
         @joined="onRoomJoined"
         @left="onRoomLeft"
>
    <ws-room-message name="message" :handler="addMessage"></ws-room-message>
    <ws-room-message name="user-list" :handler="updateUserList"></ws-room-message>
</ws-room>
```

| event name | description |
|------------|-------------|
| connected  | fired when socket has been connected (the `connection-ready` event was received from server) |
| disconnected | fired when socket was disconnected |
| joined | fired when user has joined to the room; if the connection was lost and established again ws-room will join automatically to the room |
| left | fired also when socket was disconnected |

In the `ws-room` component we can register messages we want to listen to and specify handler.
the real message we will listen is combined with the room name. In given example it will be
`chat/awesome-app/message` and `chat/awesome-app/user-list`.

Sending messages to the server is simple as calling method on the ws-room component.
That is why we want to keep a ref for it.

```js
// in some component method
this.$refs.wsRoom.sendMessage('my-message', {my: 'data'});
// it will send the message with room name: chat/awesome-app/my-message
```
