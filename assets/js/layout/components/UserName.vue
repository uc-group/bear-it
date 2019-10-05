<script>
    export default {
        functional: true,
        render(h, context) {
            const user = context.props.user
            if (user.name === user.username) {
                return  h('span', {
                    class: 'user-name'
                }, [
                    user.username
                ])
            }

            return h('span', {
                class: 'user-name'
            }, [
                user.name,
                h('span', {
                    class: 'user-name__username'
                }, user.username)
            ])
        }
    }
</script>
