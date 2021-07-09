<template>
  <v-container class="chat-page pa-0 pa-md-3">
    <v-row class="chat-page__row" v-if="channelsLoaded">
      <v-col cols="2">
        <chat-channel name="general" @click="selectRoom(project.id)" :active="currentRoom === project.id"></chat-channel>
        <div>
          <chat-channel v-for="channel in channels" :key="channel"
                        :name="channel" @click="selectRoom(channel)" :active="currentRoom === channel"></chat-channel>
          <v-divider class="my-2"></v-divider>
          <div class="text-center">
            <v-btn @click="openChannelModal" color="primary" x-small>Add new channel</v-btn>
          </div>
        </div>
        <create-channel-form
            :room="`chat/${this.project.id}`"
            :visible.sync="channelModalVisible"
            @channel:created="switchToChannel"
        ></create-channel-form>
      </v-col>
      <v-col cols="10" class="pb-0">
        <chat-room :room="currentRoom" :key="currentRoom"></chat-room>
      </v-col>
    </v-row>
    <v-row v-show="!channelsLoaded" class="chat-page__channel-loading">
      <v-col>
        <v-progress-circular indeterminate size="12" width="2"></v-progress-circular> Loading channels
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import ChatRoom from '../components/ChatRoom'
import ChatChannel from '../components/ChatChannel'
import CreateChannelForm from '../components/CreateChannelForm'
import api from '@api/chat'

export default {
  name: 'chat-page-index',
  components: {CreateChannelForm, ChatRoom, ChatChannel},
  props: {
    project: Object
  },
  data() {
    return {
      currentRoom: this.project.id,
      channelModalVisible: false,
      channelsLoaded: false,
      channels: []
    }
  },
  created() {
    this.$store.commit('PUSH_SUBTITLE', '');
    api.list(`chat/${this.project.id}`).then((list) => {
      this.channels = list.map((channel) => channel.name)
      if (window.location.hash) {
        const channelName = decodeURIComponent(window.location.hash.substr(1));
        if (this.channels.includes(channelName)) {
          this.selectRoom(channelName);
        } else {
          this.selectRoom(this.project.id)
        }
      } else {
        this.selectRoom(this.project.id)
      }

      this.channelsLoaded = true
    });
  },
  beforeDestroy() {
    this.$store.commit('POP_SUBTITLE');
  },
  methods: {
    selectRoom(name) {
      this.currentRoom = name;
      window.location.hash = name === this.project.id ? '' : name;
      history.replaceState(null, null, window.location.href.replace(/#$/, ''))

      const subtitle = name === this.project.id ? 'General' : name;
      this.$store.commit('REPLACE_SUBTITLE', `Chat #${subtitle}`)
    },
    openChannelModal() {
      this.channelModalVisible = true;
    },
    closeChannelModal() {
      this.channelModalVisible = false;
    },
    switchToChannel(name) {
      this.channels.push(name);
      this.channels.sort();
      this.selectRoom(name);
    }
  }
}
</script>

<style lang="scss" scoped>
.chat-page {
  &__row {
    display: flex;
  }

  &__channel-loading {
    font-size: 0.8em;
  }
}
</style>
