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
                                :search-input.sync="recipientsSearch"
                                @change="recipientsSearch = ''"
                                small-chips
                                deletable-chips
                                label="Destinataires"
                                multiple
                                clearable
                                auto-select-first
                                :menu-props="{ closeOnContentClick: true }"
                            ></v-autocomplete>

                            <v-select
                                v-model="filters['eventYear']"
                                :items="years"
                                label="Année de l'évènement"
                                multiple
                                clearable
                                :menu-props="{ closeOnContentClick: true }"
                            >
                            </v-select>

                            <v-container class="mt-3 pa-0 d-flex justify-center">
                                <v-btn
                                    small
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
                <template v-for="(gift, index) in gifts">

                    <v-list-item :key="gift.id" :style="{ cursor: 'pointer' }">

                        <router-link v-bind:to="'/gifts/' + gift.id" v-slot="{ href, route, navigate }">
                            <v-list-item-content :href="href" v-on:click="navigate">

                                <v-list-item-title v-text="gift.label"></v-list-item-title>

                                <v-list-item-subtitle v-if="gift.recipients.length > 0">
                                    <v-chip v-for="recipient in gift.recipients"  v-bind:key="recipient.id" small>
                                        {{ recipient.name }}
                                    </v-chip>
                                </v-list-item-subtitle>

                            </v-list-item-content>
                        </router-link>

                        <v-list-item-action>

                            <v-chip small>{{ gift.eventYear }}</v-chip>

                            <v-btn icon small v-on:click="deleteGift(gift.id)">
                                <v-icon color="grey lighten-1">mdi-delete</v-icon>
                            </v-btn>

                        </v-list-item-action>

                    </v-list-item>

                    <v-divider v-if="index + 1 < gifts.length" :key="index"></v-divider>
                </template>
            </v-list>

            <router-link to="/gifts/create" v-slot="{ href, route, navigate }">
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

    import filterMixin from '../../mixins/filterMixin.js';
    import ListSkeletonLoader from '../../components/loaders/ListSkeletonLoader.vue';

    export default {
        name: "GiftList",
        props: ['showMainFilter'],
        mixins: [filterMixin],
        components: {
            ListSkeletonLoader
        },
        data() {
            return {
                gifts: [],
                groups: [],
                recipients: [],
                years: [],
                filters: {},
                showFilter: this.showMainFilter,
                loading: false,
                recipientsSearch: '',
            };
        },
        created() {
            this.initializeFilters();
            this.fetchGroups();
            this.fetchRecipients();
            this.initializeEventYears();
        },
        watch: {
            filters: {
                handler: function(value) {
                    this.fetchGifts();
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
            fetchGifts() {

                this.loading = true;

                let url = '/api/gifts';
                const params = this.formatQueryParams(this.filters);

                url += params ? '?' + params : '';

                this.$http.get(url)
                .then( response => {
                    this.gifts = response.data['hydra:member'];
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de récupérer les cadeaux");
                })
                .finally( () => {
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

                    this.notify('error', "Impossible de récupérer les destinataires");
                });
            },
            deleteGift(id) {
                this.$http.delete('/api/gifts/' + id)
                .then( () => {
                    this.fetchGifts();
                    this.notify('success', "Le cadeau a bien été supprimé");
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de supprimer le cadeau");
                });
            },
            initializeFilters() {
                this.filters = this.$store.state.filters;
            },
            resetFilters() {
                this.filters = {};
            },
            initializeEventYears() {
                const currentYear = new Date().getFullYear();
                const startYear = currentYear - 10;
                const endYear = currentYear + 1;

                this.years = [];

                for (let i = startYear; i <= endYear; i++) {
                    this.years.push(i);
                }
            },
        }
    }

</script>

<style scoped>

    .v-list-item__action-text {
        color: rgba(0, 0, 0, .87) !important;
        font-size: 1rem;
    }

</style>
