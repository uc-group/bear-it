<template>
    <div>
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
