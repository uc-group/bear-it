<script>
    export default {
        functional: true,
        render(h, context) {
            const user = context.props.user
            if (user.name === user.username) {
                return h(user.username)
            }

            return context._v(`${user.name} [${user.username}]`)
        }
    }
</script>
