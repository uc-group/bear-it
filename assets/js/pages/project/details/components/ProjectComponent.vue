<template>
  <v-card class="project-component" :elevation="active ? 2 : 0">
    <v-card-text class="flex-grow-1">
      <div class="project-component__name text-h6">{{ name }}</div>
      <div class="project-component__icon">
        <v-icon size="72px" :color="active ? 'green' : 'red lighten-4'">{{ icon || 'mdi-package-variant' }}</v-icon>
      </div>
      <div class="project-component__description">{{ description }}</div>
    </v-card-text>
    <v-card-actions v-if="$scopedSlots.actions">
      <slot name="actions"></slot>
    </v-card-actions>
  </v-card>
</template>

<script>
export default {
  props: {
    icon: String,
    name: String,
    description: String,
    active: Boolean
  }
}
</script>

<style lang="scss" scoped>
.project-component {
  width: 200px;
  text-align: center;
  display: flex;
  flex-direction: column;

  &__name {
    margin-bottom: 5px;
  }

  &__icon {
    margin-bottom: 20px;
  }

  &__description {
    font-size: 0.8em;
  }
}
</style>
