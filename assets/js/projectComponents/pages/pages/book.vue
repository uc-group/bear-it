<template>
  <div class="book">
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'Book',
  props: {
    book: Object
  },
  data() {
    return {}
  },
  methods: {}
}
</script>
