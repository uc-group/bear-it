<template>
  <div class="user-list">
    <h4 v-show="users.length">Online users</h4>
    <div class="user-list__element" v-for="user in userList" :key="user.id">
      <v-avatar size="24" class="user-list__avatar">
        <img :src="user.avatar" :alt="user.username" :title="user.username" />
      </v-avatar>
      <user-name class="user-list__name" :user="user" short></user-name>
    </div>
  </div>
</template>

<script>
import { getUserDetailList } from '~/lib/userDetails'
import UserName from '../../../layout/components/UserName'
import { debounce } from 'lodash'

export default {
  components: {UserName},
  props: {
    users: Array
  },
  data() {
    return {
      userList: []
    }
  },
  watch: {
    users: {
      immediate: true,
      handler: debounce(async function (to) {
        this.userList = await Promise.all(Object.values(getUserDetailList(to)));
      }, 60)
    }
  }
}
</script>

<style lang="scss" scoped>
.user-list {

  &__element {
    display: flex;
    width: 100%;
    margin: 5px 0;
  }

  &__name {
    margin-left: 10px;
    font-size: 0.8em;
    white-space: nowrap;
  }
}
</style>
