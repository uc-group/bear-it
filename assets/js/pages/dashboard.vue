<template>
    <v-layout>
        <v-flex md12>
            <v-card grow>
                <v-card-title primary-title style="height: 200px; background-color: #ddd;" v-if="selectedTask">
                    <div>
                        <h3 class="headline mb-0">{{ selectedTask.title }}</h3>
                        <div>{{ selectedTask.description }}</div>
                    </div>
                </v-card-title>
                <v-card-text style="position: relative">
                    <v-btn
                            absolute
                            fab
                            top
                            right
                            color="green"
                            v-if="currentRunning !== selectedTask.id"
                            @click="play(selectedTask)"
                    >
                        <v-icon>play_arrow</v-icon>
                    </v-btn>
                    <v-btn
                            absolute
                            fab
                            top
                            right
                            color="red"
                            v-else
                            @click="stop"
                    >
                        <v-icon>stop</v-icon>
                    </v-btn>
                </v-card-text>
                <v-list two-line>
                    <template v-for="(category,index) in taskCategories">
                        <v-divider v-if="index > 0"></v-divider>
                        <v-subheader v-text="category.label">
                        </v-subheader>
                        <template v-for="(task,taskIndex) in category.tasks">
                            <v-divider v-if="taskIndex > 0" inset></v-divider>
                            <v-list-tile :key="`c${index}-${task.id}`" @click="selectTask(task)">
                                <v-layout>
                                    <v-list-tile-content>
                                        <v-list-tile-title v-text="task.title"></v-list-tile-title>
                                        <v-list-tile-sub-title v-text="task.description"></v-list-tile-sub-title>
                                    </v-list-tile-content>
                                    <div>
                                        <v-btn fab small flat v-if="task.id !== currentRunning" @click="play(task)">
                                            <v-icon>play_arrow</v-icon>
                                        </v-btn>
                                        <v-btn fab small flat v-else @click="stop">
                                            <v-icon>stop</v-icon>
                                        </v-btn>
                                        <v-btn fab small flat>
                                            <v-icon>more_horiz</v-icon>
                                        </v-btn>
                                    </div>
                                </v-layout>
                            </v-list-tile>
                        </template>
                    </template>
                </v-list>
            </v-card>
        </v-flex>
        <v-btn
            color="primary"
            fab
            fixed
            bottom
            right
        >
            <v-icon>create</v-icon>
        </v-btn>
    </v-layout>
</template>

<script>
    export default {
        data() {
            return {
                currentRunning: 'task-3',
                selectedTask: null,
                taskCategories: [
                    {
                        label: 'Today',
                        tasks: [
                            {id: 'task-1', title: 'My task one', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-2', title: 'My task two', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-3', title: 'My task three', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-4', title: 'My task four', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-5', title: 'My task five', description: 'JUST DO IT, I know you can!'}
                        ]
                    },
                    {
                        label: 'Recent',
                        tasks: [
                            {id: 'task-6', title: 'My task six', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-7', title: 'My task seven', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-8', title: 'My task eight', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-9', title: 'My task nine', description: 'JUST DO IT, I know you can!'},
                            {id: 'task-10', title: 'My task ten', description: 'JUST DO IT, I know you can!'}
                        ]
                    }
                ]
            }
        },
        created() {
            this.selectedTask = this.taskCategories[0].tasks[2];
        },
        methods: {
            selectTask(task) {
                this.selectedTask = task;
            },
            play(task) {
                this.stop();
                this.currentRunning = task.id;
            },
            stop() {
                if (!this.currentRunning) {
                    return;
                }

                this.currentRunning = null;
            }
        }
    }
</script>