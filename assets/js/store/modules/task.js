export default task => {
    return {
        state: {
            id: task.id,
            title: task.title,
            description: task.description,
            projectId: task.project.id,
            projectShortId: task.project.shortId
        }
    }
}
