<template>

    <v-container justify-center>

        <div v-if="!loading">
            <v-form
                ref="form"
                :class="editing ? '' : 'form-reading'"
                v-on:submit.prevent="onSubmit"
            >

                <v-text-field
                    v-model="event.label"
                    label="Libellé"
                    required
                    :rules="[value => !!value || 'Le libellé est obligatoire']"
                    :readonly="!editing"
                >
                </v-text-field>

                <v-text-field
                    v-model="event.year"
                    label="Année de l'évènement"
                    :readonly="!editing"
                >
                </v-text-field>

                <participant-list
                    :event="event"
                    v-on:participantDeleted="deleteParticipant"
                    v-on:participantsAdded="addParticipants"
                />

            </v-form>

        </div>

        <form-skeleton-loader
            v-model="loading"
            :button="false"
        />

    </v-container>

</template>

<script>

    import FormSkeletonLoader from '../../components/loaders/FormSkeletonLoader.vue';
    import ParticipantList from './ParticipantList.vue';
    import { deduplicateArrayBy } from '../../functions/deduplicate-array-by'

    export default {
        name: "EventDetail",
        components: {
            FormSkeletonLoader,
            ParticipantList,
        },
        data() {
            return {
                event: {
                    participants: [],
                },
                initialEvent: {},
                loading: false,
            };
        },
        created() {
            if (this.$route.meta.formMode === 'edit') {
                this.fetchEvent(this.$route.params.id);
            }

            this.$emit('formCreated');
        },
        computed: {
            editing () {
                return this.$store.state.editing;
            },
            validating () {
                return this.$store.state.validating;
            },
            canceling () {
                return this.$store.state.canceling;
            },
        },
        watch: {
            validating: function () {
                if (this.validating) {
                    this.onSubmit();
                }
            },
            canceling: function () {
                if (this.canceling) {
                    this.onCancel();
                }
            },
        },
        methods: {
            fetchEvent(id) {
                this.loading = true;

                this.$http.get('/api/events/' + id)
                    .then( response => {
                        this.event = response.data;

                        this.initialEvent = Object.assign({}, this.event);
                    })
                    .catch( error => {
                        if (error.response.status === 401) return;

                        error.status === 404
                            ? this.notify('error', "L'événement n'a pas été trouvé")
                            : this.notify('error', error.statusText);

                        this.$router.push({ name: 'home' });
                    })
                    .finally( () => {
                        this.loading = false;
                    })
                ;
            },
            onSubmit()
            {
                this.$route.meta.formMode === 'create'
                    ? this.create()
                    : this.update();
            },
            onCancel()
            {
                this.event = Object.assign({}, this.initialEvent);

                if (this.$route.meta.formMode !== 'create') {
                    this.$store.state.editing = false;
                }

                this.$store.commit('formCanceled');
            },
            create()
            {
                const event = this.event;

                this.$http.post(
                    '/api/events',
                    JSON.stringify({
                        label: event.label,
                        year: event.year,
                        participants: this.event.participants.map(element => element['@id']),
                    }),
                )
                .then( () => {
                    this.notify('success', 'L\'événement a bien été créé');
                    this.$store.commit('formValidated');

                    this.$router.push({ name: 'eventList' });
                })
                .catch( (error) => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Impossible de créer l\'événement');
                    this.$store.commit('formValidated', true);
                });
            },
            update()
            {
                const event = this.event;

                this.$http.put(
                    '/api/events/' + this.event.id,
                    JSON.stringify({
                        label: event.label,
                        year: event.year,
                        participants: this.event.participants.map(element => element['@id']),
                    }),
                )
                .then( () => {
                    this.notify('success', 'L\'événement a bien été modifié');
                    this.$store.commit('formValidated');
                })
                .catch( () => {
                    this.notify('error', 'Impossible de modifier l\'événement');
                    this.$store.commit('formValidated', true);
                });
            },
            deleteParticipant(participantToDelete) {
                this.event.participants = this
                    .event
                    .participants
                    .filter(participant => participant.id !== participantToDelete.id);
            },
            addParticipants(participantsToAdd) {
                const participants = [
                    ...this.event.participants ?? [],
                    ...participantsToAdd,
                ];

                this.event.participants = deduplicateArrayBy(participants, 'id')
            },
        }
    }

</script>

<style scoped>
    .form-reading >>> .v-input__slot::before {
        border-style: none;
    }
    .form-reading >>> .theme--light.v-label--is-disabled {
        color: rgba(0, 0, 0, .6);
    }
    .form-reading >>> .theme--light.v-input--is-disabled input {
        color: rgba(0, 0, 0, .87);
    }
    .form-reading >>> input[type="text"][disabled] {
        color: rgba(0, 0, 0, .87);
    }
    .form-reading >>> .theme--light.v-chip--disabled {
        opacity: 1;
    }
</style>
