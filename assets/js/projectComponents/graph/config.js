export default {
  name: 'Graphs',
  description: 'Flowcharts, sequence diagrams, class diagrams, etc.',
  icon: 'mdi-graph',
  menu: [
    { label: 'callback with project', link: (project) => ({ name: 'project_details', params: { id: project.id, tab: 'members' } }) }
  ]
}
