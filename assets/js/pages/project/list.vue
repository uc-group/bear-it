<template>
    <div class="projects">
        <button @click="browsingMode='card'">Card mode</button>
        <button @click="browsingMode='table'">Table mode</button>
        <project-browser :mode="browsingMode" @remove="remove"></project-browser>
        <v-row>
            <v-btn color="primary" fab fixed bottom right :to="{name: 'project_create'}">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-row>
    </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'
import storeProjectList from '~/store/modules/projectList'
import ProjectBrowser from "../../layout/components/ProjectBrowser";

const {mapState, mapActions} = createNamespacedHelpers('projectList')

export default {
    components: {ProjectBrowser},
    created() {
        this.$store.registerModule('projectList', storeProjectList)
        this.loadList()
    },
    data() {
        return {
            updating: false,
            browsingMode: 'card'
        }
    },
    methods: {
        ...mapActions(['loadList']),
        async remove(project) {
            this.updating = true
            await this.$store.dispatch('projectList/remove', project.id)
            this.updating = false
        }
    },
    beforeDestroy() {
        this.$store.unregisterModule('projectList')
    }
}
</script>
