<template>

    <v-container justify-center>

        <div v-if="!loading">

            <v-form
                ref="ideaForm"
                :class="editing ? '' : 'form-reading'"
                v-on:submit.prevent="onSubmit"
            >

                <v-text-field
                    v-model="idea.label"
                    label="Libellé"
                    required
                    :rules="[value => !!value || 'Le libellé est obligatoire']"
                    :disabled="!editing"
                >
                </v-text-field>

                <v-autocomplete
                    v-model="idea.recipientsUri"
                    :items="recipients"
                    item-text="name"
                    item-value="@id"
                    small-chips
                    deletable-chips
                    label="Destinataires"
                    multiple
                    auto-select-first
                    :disabled="!editing"
                ></v-autocomplete>

                <v-text-field
                    v-model="idea.price.value"
                    label="Prix"
                    :disabled="!editing"
                >
                </v-text-field>

            </v-form>

            <v-container class="mt-3 d-flex justify-center" v-if="!editing">
                <v-btn
                    medium
                    @click="openGiftDialog()"
                >
                    Concrétiser l'idée
                    <v-icon right color="grey darken-1">
                        mdi-lightbulb-on-outline
                    </v-icon>
                </v-btn>
            </v-container>

            <create-gift-from-idea
                v-if="!!idea.id"
                v-model="showCreateGiftDialog"
                :ideaRecipientsUri="idea.recipientsUri"
                :recipients="recipients"
                v-on:giftFromIdeaValidated="createGift"
            />

        </div>

        <form-skeleton-loader
            v-model="loading"
            :button="true"
        />

    </v-container>

</template>

<script>

    import CreateGiftFromIdea from '../../components/CreateGiftFromIdea.vue';
    import FormSkeletonLoader from '../../components/loaders/FormSkeletonLoader.vue'

    export default {
        name: "IdeaDetail",
        props: {
            editing: false,
            submitForm: false
        },
        components: {
            CreateGiftFromIdea,
            FormSkeletonLoader,
        },
        data() {
            return {
                idea: {
                    price: {}
                },
                recipients: [],
                giftDialog: {
                    recipientsUri: [],
                    eventYear: ''
                },
                showCreateGiftDialog: false,
                loading: false,
            };
        },
        created() {
            if (this.$route.meta.formMode === 'edit') {
                this.fetchIdea(this.$route.params.id);
            }
            this.fetchRecipients();
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
            fetchIdea(id) {
                this.loading = true;

                this.$http.get('/api/ideas/' + id)
                    .then( response => {
                        this.idea = response.data;
                        this.idea.recipientsUri = this.idea.recipients.map( element => element['@id'] );
                    })
                    .catch( error => {
                        if (error.response.status === 401) return;

                        error.status === 404
                            ? this.notify('error', "L'idée cadeau n'a pas été trouvé")
                            : this.notify('error', error.statusText);

                        this.$router.push({ name: 'home' });
                    })
                    .finally( () => {
                        this.loading = false;
                    })
                ;
            },
            fetchRecipients()
            {
                this.$http.get('/api/recipients')
                .then( response => {
                    this.recipients = response.data['hydra:member'];
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Impossible de récupérer les destinataires');
                });
            },
            onSubmit()
            {
                this.$route.meta.formMode === 'create'
                    ? this.create()
                    : this.update();
            },
            create()
            {
                const idea = this.idea;

                this.$http.post(
                    '/api/ideas',
                    JSON.stringify({
                        label: idea.label,
                        recipients: idea.recipientsUri,
                        price: {
                            value: parseFloat(idea.price)
                        },
                    }),
                )
                .then( () => {
                    this.notify('success', "L'idée cadeau a bien été créée");
                    this.$emit('formValidated');

                    this.$router.push({ name: 'ideaList' });
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de créer l'idée cadeau");
                    this.$emit('formValidated', true);
                });
            },
            update()
            {
                const idea = this.idea;

                this.$http.post(
                    '/api/ideas',
                    JSON.stringify({
                        label: idea.label,
                        recipients: idea.recipientsUri,
                        price: {
                            value: parseFloat(idea.price)
                        },
                    }),
                )
                .then( () => {
                    this.notify('success', "L'idée cadeau a bien été modifiée");
                    this.$emit('formValidated');
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', "Impossible de modifier l'idée cadeau");
                    this.$emit('formValidated', true);
                });
            },
            createGift(gift)
            {
                this.$http.post(
                    '/api/gifts/from_idea',
                    JSON.stringify({
                        idea: this.idea['@id'],
                        recipients: gift.recipientsUri,
                        eventYear: gift.eventYear,
                    }),
                )
                .then( () => {
                    this.notify('success', "Le cadeau a bien été créé");
                    this.$router.push({ name: 'giftList' });
                })
                .finally(() => this.showCreateGiftDialog = false);
            },
            openGiftDialog()
            {
                this.initializeGiftDialogData();
                this.showCreateGiftDialog = true;
            },
            initializeGiftDialogData()
            {
                this.giftDialog.recipientsUri = this.idea.recipientsUri;
                this.giftDialog.eventYear = new Date().getFullYear().toString();
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
