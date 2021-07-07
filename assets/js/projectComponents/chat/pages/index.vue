<template>
  <v-container class="chat-page pa-0 pa-md-3">
    <v-row class="chat-page__row">
      <v-col cols="2">
        <chat-channel name="general" @click="selectRoom(project.id)" :active="currentRoom === project.id"></chat-channel>
        <chat-channel name="test" @click="selectRoom('test')" :active="currentRoom === 'test'"></chat-channel>
      </v-col>
      <v-col cols="10">
        <chat-room :room="currentRoom" :key="currentRoom"></chat-room>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import ChatRoom from '../components/ChatRoom'
import ChatChannel from '../components/ChatChannel'

export default {
  name: 'chat-page-index',
  components: {ChatRoom, ChatChannel},
  props: {
    project: Object
  },
  data() {
    return {
      currentRoom: this.project.id
    }
  },
  methods: {
    selectRoom(name) {
      this.currentRoom = name;
    }
  }
}
</script>

<style lang="scss" scoped>
.chat-page {
  &__row {
    display: flex;
  }
}
</style>
