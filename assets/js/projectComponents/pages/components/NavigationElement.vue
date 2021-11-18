<template>
  <div class="navigation-element">
    <div class="navigation-element__row"
         :class="{ 'navigation-element__row--active': $route.params.pageId === element.page }">
      <div class="navigation-element__name">
        <slot name="name" :element="element">{{ element.name }}</slot>
      </div>
      <div class="navigation-element__add" @click="createPage">Add</div>
      <div class="navigation-element__add" @click="editPage">Edit</div>
      <div class="navigation-element__expand" @click="toggleExpand()" v-if="element.children.length">
        <v-icon v-show="!expanded">mdi-chevron-down</v-icon>
        <v-icon v-show="expanded">mdi-chevron-up</v-icon>
      </div>
    </div>
    <div class="navigation-element__children" v-if="element.children.length" v-show="expanded">
      <navigation-element :element="child" v-for="(child,i) in element.children" :key="child.page"
                          :path="currentPath"
                          @book:new-page="$listeners['book:new-page']">
        <template #name="{ element: childElement }">
          <slot name="name" :element="childElement">{{ childElement.name }}</slot>
        </template>
      </navigation-element>
    </div>
  </div>
</template>

<script>
export default {
  name: 'navigation-element',
  props: {
    element: Object,
    path: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      expandClicked: false,
      manualExpanded: false
    }
  },
  computed: {
    currentPath() {
      return [...this.path, this.element.page]
    },
    expanded() {
      if (this.expandClicked) {
        return this.manualExpanded;
      }

      const p = [];
      const extractPages = (el) => {
        if (el.children.length) {
          el.children.forEach((child) => {
            p.push(child.page);
            extractPages(child);
          })
        }
      }
      extractPages(this.element);
      return p.includes(this.$route.params.pageId);
    }
  },
  methods: {
    createPage() {
      this.$emit('book:new-page', this.currentPath);
    },
    editPage() {
      this.$router.push({
        name: 'pages_book_edit_page',
        params: {
          bookId: this.$route.params.bookId,
          pageId: this.element.page
        }
      })
    },
    toggleExpand() {
      this.expandClicked = true;
      this.manualExpanded = !this.expanded;
    }
  }
}
</script>

<style lang="scss" scoped>
.navigation-element {
  &__children {
   margin-left: 7px;
   border-left: 1px solid #ddd;
   border-bottom: 1px solid #ddd;
  }

  &__add {
    cursor: pointer;
    padding: 5px;
    border: 1px solid #ddd;
  }

  &__expand {
    cursor: pointer;
    padding: 5px;
    border: 1px solid #ddd;
  }

  &__name {
    flex: 1 1 auto;
    font-size: 0.9em;
    padding: 5px;
  }

  &__row {
    display: flex;

    &--active {
      background-color: #eea;
    }
  }
}
</style>
