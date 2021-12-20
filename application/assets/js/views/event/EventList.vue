<template>
    <v-container class="pa-0">

        <v-container v-if="!loading" justify-center class="pa-0">

            <v-list two-line>
                <template v-for="(event, index) in events">

                    <v-list-item :key="event.id" :style="{ cursor: 'pointer' }">

                        <router-link v-bind:to="'/events/' + event.id" v-slot="{ href, route, navigate }">
                            <v-list-item-content :href="href" v-on:click="navigate">

                                <v-list-item-title v-text="event.label"></v-list-item-title>

                            </v-list-item-content>
                        </router-link>

                        <v-list-item-action>

                            <v-list-item-action-text v-text="event.year"></v-list-item-action-text>

                            <v-btn icon small v-on:click="deleteEvent(event.id)">
                                <v-icon color="grey lighten-1">mdi-delete</v-icon>
                            </v-btn>

                        </v-list-item-action>

                    </v-list-item>

                    <v-divider v-if="index + 1 < events.length" :key="index"></v-divider>
                </template>
            </v-list>

            <router-link to="/events/create" v-slot="{ href, route, navigate }">
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

    import ListSkeletonLoader from '../../components/loaders/ListSkeletonLoader.vue';

    export default {
        name: "EventList",
        components: {
            ListSkeletonLoader
        },
        data() {
            return {
                events: [],
                loading: true,
            };
        },
        created() {
            this.fetchEvents()
        },
        methods: {
            fetchEvents() {

                let url = '/api/events';

                this.$http.get(url)
                .then( response => {
                    this.events = response.data['hydra:member'];
                    console.log(this.events)
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de récupérer les cadeaux");
                })
                .finally( () => {
                    this.loading = false;
                });
            },
            deleteEvent(id) {
                this.$http.delete('/api/events/' + id)
                .then( () => {
                    this.fetchGifts();
                    this.notify('success', "L'événement a bien été supprimé");
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de supprimer l'événement");
                });
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
