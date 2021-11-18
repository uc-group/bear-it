<template>
  <div class="book fill-height" v-if="book">
    <v-container>
      <v-row>
        <v-col>
          <v-btn small @click="navigationVisible = !navigationVisible">Toggle navigation</v-btn>
          <div class="book__container">
            <div class="book__navigation" v-show="navigationVisible">
              <navigation-element v-for="child in navigation.children" :element="child" :key="child.page" @book:new-page="createPage">
                <template #name="{ element }">
                  <router-link :to="goToPage(element.page)">{{ element.name }}</router-link>
                </template>
              </navigation-element>
              <hr />
              <v-btn small :loading="creatingNewPage" @click="createPage">New page</v-btn>
            </div>
            <div class="book__main">
              <router-view></router-view>
            </div>
          </div>
        </v-col>
      </v-row>
    </v-container>
    <new-page-dialog v-model="newPageDialog" :project-id="project.id" v-if="project"
                     @page:created="pageCreated"></new-page-dialog>
  </div>
</template>

<script>
import api from '../api';
import NavigationElement from '../components/NavigationElement';
import NewPageDialog from "../components/NewPageDialog";
import clone from '@lib/clone';

export default {
  name: 'Book',
  components: {NewPageDialog, NavigationElement },
  async beforeRouteEnter(to, from, next) {
    const book = to.params.book || await api.getBook(to.params.bookId);
    if (!to.params.pageId && book.navigation.children.length) {
      next({
        name: 'pages_book_page',
        params: {
          project: to.params.project,
          bookId: to.params.bookId,
          pageId: book.navigation.children[0].page,
          book: book
        }
      });
    } else {
      next((vm) => {
        vm.setBook(book);
      });
    }
  },
  async beforeRouteUpdate(to, from, next) {
    if (this.book.id !== to.params.bookId) {
      this.setBook(await api.getBook(to.params.bookId));
    }
    next();
  },
  props: {
    project: Object
  },
  data() {
    return {
      book: null,
      creatingNewPage: false,
      newPageDialog: false,
      newPagePath: null,
      navigation: { name: '', page: null, children: [] },
      navigationVisible: true
    }
  },
  methods: {
    async pageCreated(page) {
      const path = clone(this.newPagePath);
      const newNavigation = JSON.parse(JSON.stringify(this.navigation));
      if (!path || !path.length) {
        newNavigation.children.push({
          name: page.name,
          page: page.id,
          children: []
        });
      } else {
        const currentNode = path.reduce((acc, pageId) => {
          if (!acc) {
            return undefined;
          }

          return acc.children.find((el) => el.page === pageId) || undefined
        }, newNavigation);
        if (currentNode) {
          currentNode.children.push({
            name: page.name,
            page: page.id,
            children: []
          })
        }
      }
      this.navigation = newNavigation;
      this.newPageDialog = false;
      await api.updateNavigation(this.book.id, clone(newNavigation));
      this.$router.push({
        name: 'book_page',
        params: {
          project: this.project,
          bookId: this.book.id,
          pageId: page.id
        }
      })
    },
    setBook(book) {
      this.navigation = clone(book.navigation);
      this.$store.commit('PUSH_SUBTITLE', book.name);
      this.book = book;
    },
    createPage(path) {
      this.newPagePath = path;
      this.newPageDialog = true;
    },
    goToPage(page) {
      return {
        name: 'pages_book_page',
        params: {
          project: this.project,
          book: this.book.id,
          pageId: page
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.book {
  display: flex;

  &__container {
    display: flex;
  }

  &__navigation {
    width: 300px;
  }

  &__main {
    padding: 0 20px;
    max-width: 900px;
    flex: 1 1 auto;
  }
}
</style>
