<template>
  <v-container class="chat-page pa-0 pa-md-3">
    <ws-room :name="`notification/chat/${project.id}`">
      <ws-room-message name="channel-created" :handler="addChannel"></ws-room-message>
    </ws-room>
    <v-row class="chat-page__row" v-if="channelsLoaded">
      <v-col cols="2">
        <chat-channel name="general" @click="selectChannel(null)" :active="channel === null"></chat-channel>
        <div>
          <chat-channel v-for="c in channels" :key="c"
                        :name="c" @click="selectChannel(c)" :active="c === channel"></chat-channel>
          <v-divider class="my-2"></v-divider>
          <div class="text-center">
            <v-btn @click="openChannelModal" color="primary" x-small>Add new channel</v-btn>
          </div>
        </div>
        <create-channel-form
            :room="`chat/${this.project.id}`"
            :visible.sync="channelModalVisible"
            @channel:created="switchToChannel"
            close-on-create
        ></create-channel-form>
      </v-col>
      <v-col cols="10" class="pb-0">
        <chat-room :room="project.id" :channel="channel" :key="currentRoom"
          @channel:selected="switchToChannel"
          :channels="channels"></chat-room>
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
import WsRoom from '../../../components/WsRoom'
import WsRoomMessage from '../../../components/WsRoomMessage'

export default {
  name: 'chat-page-index',
  components: {CreateChannelForm, ChatRoom, ChatChannel, WsRoom,
    WsRoomMessage},
  props: {
    project: Object
  },
  data() {
    return {
      channel: null,
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
          this.selectChannel(channelName);
        } else {
          this.selectChannel(null)
        }
      } else {
        this.selectChannel(null)
      }

      this.channelsLoaded = true
    });
  },
  beforeDestroy() {
    this.$store.commit('POP_SUBTITLE');
  },
  computed: {
    currentRoom() {
      return this.channel ? `${this.project.id}/${this.channel}` : this.project.id;
    }
  },
  methods: {
    selectChannel(name) {
      this.channel = name;
      window.location.hash = name || '';
      history.replaceState(null, null, window.location.href.replace(/#$/, ''))

      const subtitle = !name ? 'general' : name;
      this.$store.commit('REPLACE_SUBTITLE', `Chat #${subtitle}`)
    },
    openChannelModal() {
      this.channelModalVisible = true;
    },
    closeChannelModal() {
      this.channelModalVisible = false;
    },
    switchToChannel(name) {
      if (name) {
        this.addChannel(name);
      }
      this.selectChannel(name);
    },
    addChannel(name) {
      if (!this.channels.includes(name)) {
        this.channels.push(name);
        this.channels.sort();
      }
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
