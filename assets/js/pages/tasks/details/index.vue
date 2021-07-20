<template>
    <v-layout>
        <v-container class="pa-0 pa-md-3">
            <v-row>
                <v-col>
                    <h2>{{ currentTask.title }}</h2>
                </v-col>
            </v-row>
            <div>{{ currentTask.id }}</div>
            <div>{{ currentTask.title }}</div>
            <div>{{ currentTask.description }}</div>
            <div>{{ currentTask.projectId }}</div>
            <div>{{ currentTask.projectShortId }}</div>
        </v-container>
    </v-layout>
</template>

<script>
import taskStore from '~/store/modules/task'

export default {
    props: {
        task: Object
    },
    created() {
        this.$store.registerModule('task', taskStore(this.task))
        document.title = "[" + this.currentTask.id + "] " + this.currentTask.title;
    },
    beforeDestroy() {
        document.title = 'Bear-IT';
        this.$store.unregisterModule('task')
    },
    computed: {
        currentTask() {
            return this.$store.state.task
        }
    }
}
</script>
