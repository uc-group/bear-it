<template>
    <v-menu bottom :offset-y="true">
        <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
                <v-icon>mdi-account</v-icon>
            </v-btn>
        </template>

        <v-list dense>
            <v-list-item two-line>
                <v-list-item-avatar>
                        <img :src="user.avatar" :alt="user.login" :title="user.name"/>
                </v-list-item-avatar>
                <v-list-item-content>
                    <v-list-item-title >{{ user.name }}</v-list-item-title>
                    <v-list-item-subtitle v-if="user.name !== user.login">{{ user.login }}</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item link>
                <v-list-item-icon>
                    <v-icon>mdi-account-edit</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Profile (@TODO)</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link>
                <v-list-item-icon>
                    <v-icon>mdi-settings</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Settings (@TODO)</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-divider></v-divider>
            <v-list-item link href="/logout">
                <v-list-item-icon>
                    <v-icon>logout</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title>Logout</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
    </v-menu>
</template>

<script>
    import { mapState } from 'vuex'

    export default {
        computed: {
            ...mapState(['user'])
        }
    }
</script>
