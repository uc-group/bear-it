<template>
    <v-autocomplete
        v-model="selectedUsers"
        :search-input.sync="term"
        :items="items"
        label="Selected users"
        :loading="loading"
        :disabled="disabled"
        item-value="username"
        item-text="name"
        cache-items
        chips
        multiple
    >
        <template v-slot:selection="data">
            <v-chip
                v-bind="data.attrs"
                :input-value="data.selected"
                close
                @click="data.select"
                @click:close="remove(data.item)"
            >
                <v-avatar left>
                    <v-img :src="data.item.avatar"></v-img>
                </v-avatar>
                <user-name :user="data.item"></user-name>
            </v-chip>
        </template>
        <template v-slot:item="data">
            <v-list-item-avatar>
                <img :src="data.item.avatar" />
            </v-list-item-avatar>
            <v-list-item-content>
                <v-list-item-title>{{ data.item.name }}</v-list-item-title>
                <v-list-item-subtitle v-if="data.item.name != data.item.username">{{ data.item.username }}</v-list-item-subtitle>
            </v-list-item-content>
        </template>
    </v-autocomplete>
</template>

<script>
    import api from '@api/user'
    import UserName from './UserName.vue'

    export default {
        components: {
            UserName
        },
        props: {
            currentUsers: {
                type: Array,
                default() {
                    return []
                }
            },
            selected: Array,
            disabled: Boolean
        },
        data() {
            return {
                items: [],
                term: null,
                loading: false
            }
        },
        created() {
            this.timeout = null
        },
        computed: {
            selectedUsers: {
                get() {
                    return this.selected;
                },
                set(value) {
                    this.$emit('update:selected', value)
                }
            }
        },
        watch: {
            term(val) {
                clearTimeout(this.timeout)
                this.timeout = setTimeout(() => {
                    this.findUsers(val)
                }, 500)
            }
        },
        methods: {
            findUsers(term) {
                this.loading = true
                api.find(term).then(response => {
                    this.items = response.filter(user => !this.currentUsers.includes(user.username))
                    this.loading = false
                });

            },
            remove(item) {
                const index = this.selectedUsers.indexOf(item.username)
                if (index >= 0) {
                    this.selectedUsers.splice(index, 1)
                }
            }
        }
    }
</script>
