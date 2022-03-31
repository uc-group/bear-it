<template>
    <div class="dashboard elevation-13">
        <v-row class="dashboard__row">
            <v-col cols="4" class="dashboard__page-list">
                <div class="page-list__new-page-action">
                    <v-btn class="mx-2" dark x-small color="indigo" :to="newPage">
                        New page
                    </v-btn>
                </div>
                <div class="page-list__file" :class="pageClass(page)" v-for="page in pages" @click="showPage(page)">
                    {{ page.name }}
                </div>
            </v-col>
            <v-col cols="8" class="dashboard__viewer">
              <v-btn v-if="selectedPage" class="mx-2 mr-11" fab dark x-small absolute top right color="green" :to="showPageRoute">
                <v-icon dark>
                  mdi-eye
                </v-icon>
              </v-btn>
                <v-btn v-if="selectedPage" class="mx-2" fab dark x-small absolute top right color="cyan" :to="editPage">
                    <v-icon dark>
                        mdi-pencil
                    </v-icon>
                </v-btn>
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
                params: { pageId: this.selectedPage.id }
            }
        },
        showPageRoute() {
            return {
              name: 'pages_show',
              params: { pageId: this.selectedPage.id }
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
.dashboard {
    flex: 1 1 auto;

    &__row {
        height: 100%;
    }

    &__page-list {
        display: flex;
        flex-direction: column;

        background-color: #e0e0e0;
        color: #1e1e1e;
        border-right: solid 1px #f0f0f0;
        height: 100%;
        padding-right: 0;
    }

    &__viewer {
        background-color: #fafafa;
        position: relative;
    }
}

.page-list {
    &__new-page-action {
        margin-bottom: 5px;
        font-size: 0.7rem;
    }

    &__file {
        padding: 3px 0;
        text-align: center;
        cursor: pointer;

        &--active {
            background-color: #cecece;
        }

        &:hover {
            background-color: #efefef;
        }
    }
}
</style>
