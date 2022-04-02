<template>
    <v-layout>
        <v-container class="pa-0 pa-md-3">
            <v-row>
              <v-card :elevation="2" class="flex-grow-1">
                <v-card-title>
                  <h2>
                    <v-icon>mdi-format-list-checks</v-icon>
                    #{{ taskNumber }} - {{ currentTask.title }}
                  </h2>
                </v-card-title>
                <v-card-text>
                  <v-container class="pa-0 pa-md-3">
                    <v-row class="flex-wrap-reverse">
                      <v-col cols="12" md="8">
                        <div>{{ currentTask.description }}</div>
                      </v-col>
                      <v-col cols="12" md="4">
                        INFO column
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card-text>
                <v-card-actions>
                  @todo
                </v-card-actions>
              </v-card>
            </v-row>
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
        },
        taskNumber() {
          return this.currentTask?.id.split('-')[1]
        }
    }
}
</script>
