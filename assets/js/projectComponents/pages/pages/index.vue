<template>
    <div class="component-pages fill-height">
        <div class="component-pages__books">
          <div class="actions">
            <create-new-book :project-id="project.id"/>
          </div>
          <div class="component-pages__bookshelf">
            <template  v-for="book in books">
              <router-link :to="{ name: 'pages_book', params: { bookId: book.id } }">
                <div class="book elevation-2">{{ book.name }}</div>
              </router-link>
            </template>
          </div>
        </div>
        <dashboard :pages="pages"></dashboard>
    </div>
</template>

<script>
import Dashboard from '../components/Dashboard'
import api from '../api'
import CreateNewBook from '../components/CreateNewBook';

export default {
    name: 'pages-page-index',
    components: { CreateNewBook, Dashboard },
    props: {
        project: Object,
        pages: Array,
        books: Array
    }
}
</script>

<style lang="scss" scoped>
.component-pages {
    display: flex;
    flex-direction: column;

  &__books {
    display: flex;
    flex-direction: column;
    padding: 10px;
    margin: 10px;
  }

  &__bookshelf {
    display: flex;
    flex-wrap: wrap;
  }
}

.book {
  width: 100px;
  height: 150px;
  margin: 5px;
  font-size: 11px;
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
