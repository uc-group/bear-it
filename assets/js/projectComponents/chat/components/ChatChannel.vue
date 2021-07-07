<template functional>
  <div v-on="listeners" class="chat-channel pa-1 my-1" :class="{'chat-channel--active': props.active}">
    <span class="chat-channel__hash">#</span>
    <span class="chat-channel__name">{{ props.name }}</span>
  </div>
</template>

<script>
export default {
  props: {
    name: String,
    active: Boolean
  }
}
</script>

<style lang="scss" scoped>
.chat-channel {
  $self: &;
  cursor: pointer;
  background-color: #eee;
  border-radius: 5px;
  font-size: 0.8em;

  &:hover {
    background-color: #ccc;
  }

  &__hash {
    color: #aaa;
  }

  &--active {
    background-color: lighten(#2196F3, 30%);
    cursor: default;

    &:hover {
      background-color: lighten(#2196F3, 30%);
    }

    #{$self}__hash {
      color: lighten(#2196F3, 10%);
    }
  }

  &__name {
    font-weight: 600;
  }
}
</style>

