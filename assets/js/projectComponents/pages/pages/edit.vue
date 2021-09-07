<template>
    <div>
        <page-form :init-data="page" @submit="savePage"></page-form>
    </div>
</template>

<script>
import PageForm from '../components/PageForm'
import api from '../api'

export default {
    name: 'pages-page-index',
    components: { PageForm },
    props: {
        page: Object
    },
    methods: {
        savePage({ name, content }) {
            api.save(this.page.id, name, content).then((id) => {
                this.$store.dispatch('alerts/addMessage', {
                    text: `Page "${name}" successfully saved`,
                    type: 'success'
                })

                this.$router.push({ name: 'pages_index' })
            })
        }
    }
}
</script>
