<template>
    <div>
        <page-form :init-data="page" @submit="savePage" v-if="page" :key="page.id"></page-form>
    </div>
</template>

<script>
import PageForm from '../components/PageForm'
import api from '../api'

export default {
    name: 'pages-page-index',
    components: { PageForm },
    props: {
      pageId: String
    },
    async beforeRouteEnter(to, from, next) {
        const page = await api.get(to.params.pageId);
        next((vm) => {
          vm.setPage(page)
        })
    },
    async beforeRouteUpdate(to, from, next) {
        this.setPage(await api.get(to.params.pageId));
        next();
    },
    data() {
      return {
        page: null,
      }
    },
    methods: {
        savePage({ name, content }) {
            api.save(this.page.id, name, content).then((id) => {
                this.$store.dispatch('alerts/addMessage', {
                    text: `Page "${name}" successfully saved`,
                    type: 'success'
                })

                if (this.$route.name === 'pages_book_edit_page') {
                  this.$router.push({ name: 'pages_book_page', params: {
                      bookId: this.$route.params.bookId,
                      pageId: this.page.id
                    } });
                } else {
                  this.$router.push({name: 'pages_index'})
                }
            })
        },
        setPage(page) {
          this.page = page;
        }
    }
}
</script>
