<template>
  <div class="alert-list">
    <v-alert :type="message.type" v-for="message in messages" :key="message.id">{{ message.text }}</v-alert>
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState({
      messages: state => state.alerts.messages
    })
  }
}
</script>

<style lang="scss" scoped>
.alert-list {
  right: 0;
  position: fixed;
  height: 100vh;
  z-index: 1000;
  padding: 65px 10px;
}
</style>
