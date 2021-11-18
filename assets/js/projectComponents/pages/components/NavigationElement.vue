<template>
  <div class="navigation-element">
    <div class="navigation-element__row">
      <div class="navigation-element">{{ element.name }}</div>
      <div class="navigation-element" @click="createPage">+</div>
    </div>
    <div class="navigation-element__children" v-if="element.children">
      <navigation-element :element="child" v-for="(child,i) in element.children" :key="child.page"
                          :path="`${path}.${child.path}`"
                          @book:new-page="$listeners['book:new-page']">
      </navigation-element>
    </div>
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid';
export default {
  name: 'navigation-element',
  props: {
    element: Object,
    path: String
  },
  methods: {
    createPage() {
      this.$emit('book:new-page', {
        path: this.path,
        page: uuid(),
        name: 'somePage'
      });
    }
  }
}
</script>
