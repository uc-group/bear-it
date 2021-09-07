<template>
    <div>
        <page-form @submit="createPage"></page-form>
    </div>
</template>

<script>
import PageForm from '../components/PageForm'
import api from '../api'

export default {
    name: 'pages-page-index',
    components: { PageForm },
    props: {
        project: Object
    },
    methods: {
        createPage({ name, content }) {
            api.create(this.project.id, name, content).then(() => {
                this.$store.dispatch('alerts/addMessage', {
                    text: `Page "${name}" successfully created`,
                    type: 'success'
                })
                this.$router.push({ name: 'pages_index' })
            })
        }
    }
}
</script>
