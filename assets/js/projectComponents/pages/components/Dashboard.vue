<template>
    <div class="dashboard__container">
        <v-row class="dashboard__top-bar" justify="end">
            <v-col cols="4" class="top-bar__actions">
                <v-btn class="mx-2" fab dark color="indigo" :to="newPage">
                    <v-icon dark>
                        mdi-plus
                    </v-icon>
                </v-btn>
                <v-btn v-if="selectedPage" class="mx-2" fab dark color="cyan" :to="editPage">
                    <v-icon dark>
                        mdi-pencil
                    </v-icon>
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col cols="2" class="dashboard__page-list">
                <div class="page-list__file" :class="pageClass(page)" v-for="page in pages" @click="showPage(page)">
                    {{ page.name }}
                </div>
            </v-col>
            <v-col cols="10" class="dashboard__viewer">
                <markdown-viewer v-if="selectedPage" :content="selectedPage.content"></markdown-viewer>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import MarkdownViewer from './MarkdownViewer'

export default {
    name: "PageList",
    components: { MarkdownViewer },
    props: {
        pages: {
            type: Array,
            default: []
        }
    },
    data() {
        return {
            selectedPage: null
        }
    },
    computed: {
        editPage() {
            return {
                name: 'pages_edit',
                params: { page: this.selectedPage.id }
            }
        },
        newPage() {
            return {
                name: 'pages_create'
            }
        },
    },
    methods: {
        showPage(page) {
            this.selectedPage = page
        },
        pageClass(page) {
            return {
                'page-list__file--active': this.selectedPage === page
            }
        }
    }
}
</script>

<style lang="scss" scoped>
.page-list {
    &__file {
        height: 30px;
        background-color: lightgray;
        border-bottom: 1px black solid;
        text-align: center;
        cursor: pointer;

        &--active {
            background-color: green;
        }

        &:hover {
            background-color: lightblue;
        }
    }
}
</style>
