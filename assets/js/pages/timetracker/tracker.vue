<template>
  <v-layout>
    <v-flex md12>
      <v-card grow>
        <v-card-title
          primary-title
          style="height: 200px; background-color: #ddd;"
          v-if="selectedTask"
        >
          <p>{{ selectedTask.title }}</p>
        </v-card-title>
        <v-card-text style="position: relative">
          {{ selectedTask.description }}
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
          <v-btn absolute fab top right color="red" v-else @click="stop">
            <v-icon>stop</v-icon>
          </v-btn>
        </v-card-text>
        <v-list two-line>
          <template v-for="(category,index) in taskCategories">
            <v-divider v-if="index > 0" :key="`hr-${index}`"></v-divider>
            <v-subheader v-text="category.label" :key="`label-${index}`"></v-subheader>
            <template v-for="(task,taskIndex) in category.tasks">
              <v-divider v-if="taskIndex > 0" inset :key="`hr-${index}-${taskIndex}`"></v-divider>
              <v-list-item :key="`c${index}-${task.id}`" @click="selectTask(task)">
                <v-list-item-content>
                  <v-list-item-title v-text="task.title"></v-list-item-title>
                  <v-list-item-subtitle v-text="task.description"></v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action>
                  <v-btn fab small text v-if="task.id !== currentRunning" @click.stop="play(task)" @mousedown.stop @touchstart.stop>
                    <v-icon color="green">play_arrow</v-icon>
                  </v-btn>
                  <v-btn fab small text v-else @click.stop="stop" @mousedown.stop @touchstart.stop>
                    <v-icon color="red">stop</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </template>
          </template>
        </v-list>
      </v-card>
    </v-flex>
    <v-btn color="primary" fab fixed bottom right @click="$router.push({name: 'task_create'})">
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
            {
              id: 'task-1',
              title: 'My task one',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-2',
              title: 'My task two',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-3',
              title: 'My task three',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-4',
              title: 'My task four',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-5',
              title: 'My task five',
              description: 'JUST DO IT, I know you can!'
            }
          ]
        },
        {
          label: 'Recent',
          tasks: [
            {
              id: 'task-6',
              title: 'My task six',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-7',
              title: 'My task seven',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-8',
              title: 'My task eight',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-9',
              title: 'My task nine',
              description: 'JUST DO IT, I know you can!'
            },
            {
              id: 'task-10',
              title: 'My task ten',
              description: 'JUST DO IT, I know you can!'
            }
          ]
        }
      ]
    }
  },
  methods: {
    selectTask(task) {
      this.selectedTask = task
    },
    play(task) {
      this.stop()
      this.currentRunning = task.id
      this.selectedTask = task
    },
    stop() {
      if (!this.currentRunning) {
        return
      }

      this.currentRunning = null
    }
  },
  created() {
    this.selectedTask = this.taskCategories[0].tasks[2]
  }
}
</script>