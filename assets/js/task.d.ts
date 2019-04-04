export interface Task {
  id: string
  title: string
  description: string
}

export interface TaskCategory {
  label: string
  tasks: Task[]
}
