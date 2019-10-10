<template>
    <v-menu>
        <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
                <v-icon>more_vert</v-icon>
            </v-btn>
        </template>
        <v-list>
            <v-list-item link @click.prevent>
                <v-list-item-icon>
                    <v-icon>mdi-account</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    Profile (@TODO)
                </v-list-item-content>
            </v-list-item>
            <v-list-item @click="removeMember(member.username)" v-if="access.members.remove(member.username)">
                <v-list-item-icon>
                    <v-icon>mdi-account-remove</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    Remove member
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
    import { createNamespacedHelpers } from 'vuex'

    const { mapActions, mapGetters } = createNamespacedHelpers('project')

    export default {
        props: {
            member: Object,
        },
        computed: {
            ...mapGetters(['access'])
        },
        methods: {
            ...mapActions(['removeMember'])
        }
    }
</script>
