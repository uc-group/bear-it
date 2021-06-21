<script>
    export default {
        functional: true,
        props: {
          short: Boolean,
          user: Object
        },
        render(h, context) {
            const user = context.props.user
            if (user.name === user.username || context.props.short) {
                return  h('span', {
                    class: ['user-name', context.data.staticClass],
                    style: [context.data.staticStyle || [], context.data.style || {}]
                }, [
                    user.name
                ])
            }

            return h('span', {
                class: ['user-name', context.data.staticClass],
                style:[context.data.staticStyle || [], context.data.style || {}]
            }, [
                h('span', {
                    class: 'user-name__name'
                }, user.name),
                h('span', {
                    class: 'user-name__username'
                }, user.username)
            ])
        }
    }
</script>
