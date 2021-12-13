<template>
    <v-container class="pa-0">

        <v-navigation-drawer v-model="showFilter" fixed right width=300 temporary hide-overlay>

            <div class="drawer-container">

                <v-card elevation="0">

                    <v-card-title>
                        Filtres
                    </v-card-title>

                    <v-card-text>
                        <v-form v-on:submit.prevent="onSubmit">

                            <v-text-field
                                v-model="filters['label']"
                                label="Libellé"
                                clearable
                            >
                            </v-text-field>

                            <v-select
                                v-model="filters['recipients.group.id']"
                                :items="groups"
                                label="Groupe"
                                item-text="label"
                                item-value="@id"
                                clearable
                            >
                            </v-select>

                            <v-autocomplete
                                v-model="filters['recipients.id[]']"
                                :items="recipients"
                                item-text="name"
                                item-value="@id"
                                small-chips
                                deletable-chips
                                label="Destinataires"
                                multiple
                                clearable
                                auto-select-first
                            ></v-autocomplete>

                            <v-container class="mt-3 pa-0 d-flex justify-center">
                                <v-btn
                                    medium
                                    @click="resetFilters()"
                                >
                                    Réinitialiser
                                    <v-icon right color="grey darken-1">
                                        mdi-refresh
                                    </v-icon>
                                </v-btn>
                            </v-container>

                        </v-form>
                    </v-card-text>

                </v-card>

            </div>

        </v-navigation-drawer>

        <v-container v-if="!loading" justify-center class="pa-0">

            <v-list two-line>
                <template v-for="(idea, index) in ideas">

                    <v-list-item :key="idea.id" :style="{ cursor: 'pointer' }">

                        <router-link v-bind:to="'/ideas/' + idea.id" v-slot="{ href, navigate }" :disabled="showFilter">
                            <v-list-item-content :href="href" v-on:click="navigate">

                                <v-list-item-title v-text="idea.label"></v-list-item-title>

                                <v-list-item-subtitle v-if="idea.recipients.length > 0">
                                    <v-chip v-for="recipient in idea.recipients" v-bind:key="recipient.id" small>
                                        {{ recipient.name }}
                                    </v-chip>
                                </v-list-item-subtitle>

                            </v-list-item-content>
                        </router-link>

                        <v-list-item-action>
                            <v-btn icon v-on:click="deleteIdea(idea.id)">
                                <v-icon color="grey lighten-1">mdi-delete</v-icon>
                            </v-btn>
                        </v-list-item-action>

                    </v-list-item>

                    <v-divider v-if="index + 1 < ideas.length" :key="index"></v-divider>
                </template>
            </v-list>

            <router-link to="/ideas/create" v-slot="{ href, route, navigate }">
                <v-btn class="mx-2" fab dark fixed bottom right color="teal darken-1" :href="href" v-on:click="navigate">
                    <v-icon dark>mdi-plus</v-icon>
                </v-btn>
            </router-link>

        </v-container>

        <list-skeleton-loader
            v-model="loading"
            :numberOfItems="5"
        />

    </v-container>
</template>

<script>

    import filterMixin from '../../mixins/filterMixin.js'
    import ListSkeletonLoader from '../../components/loaders/ListSkeletonLoader.vue'

    export default {
        name: "IdeaList",
        props: ['showMainFilter'],
        mixins: [filterMixin],
        components: {
            ListSkeletonLoader
        },
        data() {
            return {
                ideas: [],
                groups: [],
                recipients: [],
                filters: {},
                showFilter: this.showMainFilter,
                loading: true,
            };
        },
        created() {
            this.initializeFilters();
            this.fetchGroups();
            this.fetchRecipients();
        },
        watch: {
            filters: {
                handler: function(value) {
                    this.fetchIdeas();
                    this.$store.commit('saveFilters', value);
                },
                deep: true
            },
            showMainFilter: {
                handler(value) {
                    this.showFilter = value;
                },
            },
            showFilter: {
                handler(value) {
                    this.$emit('showMainFilterUpdated', value);
                },
            },
        },
        methods: {
            fetchIdeas() {

                let url = '/api/ideas';
                const params = this.formatQueryParams(this.filters);

                url += params ? '?' + params : '';

                this.$http.get(url)
                .then( response => {
                    this.ideas = response.data['hydra:member'];
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de récupérer les idées cadeaux");
                })
                .then( () => {
                    this.loading = false;
                });
            },
            fetchGroups()
            {
                this.$http.get('/api/groups')
                .then( response => {
                    this.groups = response.data['hydra:member'];
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de récupérer les groupes");
                });
            },
            fetchRecipients()
            {
                this.$http.get('/api/recipients')
                .then( response => {
                    this.recipients = response.data['hydra:member'];
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de récupérer les groupes");
                });
            },
            deleteIdea(id) {
                this.$http.delete('/api/ideas/' + id)
                .then( () => {
                    this.fetchIdeas();
                    this.notify('success', "L'idée cadeau a bien été supprimée");
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de supprimer l'idée cadeau");
                });
            },
            initializeFilters() {
                this.filters = this.$store.state.filters;
            },
            resetFilters() {
                this.filters = {};
            },
        }
    }

</script>

<style scoped>

</style>
