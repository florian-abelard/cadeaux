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
                    :disabled="!editing"
                >
                </v-text-field>

                <v-text-field
                    v-model="event.year"
                    label="Année de l'évènement"
                    :disabled="!editing"
                >
                </v-text-field>

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

    export default {
        name: "EventDetail",
        props: {
            editing: false,
            submitForm: false
        },
        components: {
            FormSkeletonLoader,
        },
        data() {
            return {
                event: [],
                loading: false,
            };
        },
        created() {
            if (this.$route.meta.formMode === 'edit') {
                this.fetchEvent(this.$route.params.id);
            }
            this.$emit('formCreated');
        },
        watch: {
            submitForm: function () {
                if (this.submitForm) {
                    this.onSubmit();
                }
            }
        },
        methods: {
            fetchEvent(id) {
                this.loading = true;

                this.$http.get('/api/events/' + id)
                    .then( response => {
                        this.event = response.data;
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
            create()
            {
                const event = this.event;

                this.$http.post(
                    '/api/events',
                    JSON.stringify({
                        label: event.label,
                        year: event.year,
                    }),
                )
                .then( () => {
                    this.notify('success', 'L\'événement a bien été créé');
                    this.$emit('formValidated');

                    this.$router.push({ name: 'eventList' });
                })
                .catch( (error) => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Impossible de créer l\'événement');
                    this.$emit('formValidated', true);
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
                    }),
                )
                .then( () => {
                    this.notify('success', 'L\'événement a bien été modifié');
                    this.$emit('formValidated');
                })
                .catch( () => {
                    this.notify('error', 'Impossible de modifier l\'événement');
                    this.$emit('formValidated', true);
                });
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
