<template>
    <div class="component-pages">
        <dashboard :pages="pages"></dashboard>
    </div>
</template>

<script>
import Dashboard from '../components/Dashboard'
import api from '../api'

export default {
    name: 'pages-page-index',
    components: { Dashboard },
    props: {
        project: Object
    },
    data() {
        return {
            pages: []
        }
    },
    created() {
        api.list(this.project.id).then(pages => {
            this.pages = pages
        })
    }
}
</script>

<style lang="scss">
.component-pages {
    height: 100%;
}
</style>
