<script>
import CardList from "./ProjectCardList";
import Table from "./ProjectTableList";

export default {
    name: "ProjectBrowser",
    functional: true,
    props: {
        mode: {
            type: String,
            validator: value => {
                return ['card', 'table'].indexOf(value) !== -1
            }
        }
    },
    render(createElement, { props, data, children, listeners }) {
        function appropriateListComponent() {
            return props.mode === 'card' ? CardList : Table
        }

        return createElement(
            appropriateListComponent(),
            {
                data: data,
                props: props,
                on: listeners
            },
            children
        )
    }
}
</script>
