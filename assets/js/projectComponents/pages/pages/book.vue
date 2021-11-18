<template>
  <div class="book fill-height">
    <v-container>
      <v-row>
        <v-col>
          <h1>{{ book.name }}</h1>
          <div class="book__navigation">
            <navigation-element :element="navigation" @book:new-page="createPage" path="[ROOT]"/>
          </div>
          <div class="book__main">
            <router-view></router-view>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>

<script>
import NavigationElement from '../components/NavigationElement';
export default {
  name: 'Book',
  components: { NavigationElement },
  props: {
    book: Object
  },
  data() {
    return {
      navigation: {}
    }
  },
  created() {
    this.navigation = JSON.parse(JSON.stringify(this.book.navigation));
    this.navigation.name = this.book.name;
  },
  methods: {
    createPage(data) {
      const newNavigation = JSON.parse(JSON.stringify(this.navigation));
      data.path.split('.');
      console.log(data.path.split('.'));
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
