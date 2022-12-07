<template>

    <v-container justify-center>

        <div v-if="!loading">
            <v-form
                ref="giftForm"
                :class="editing ? '' : 'form-reading'"
                v-on:submit.prevent="onSubmit"
            >

                <v-text-field
                    v-model="gift.label"
                    label="Libellé"
                    required
                    :rules="[value => !!value || 'Le libellé est obligatoire']"
                    :readonly="!editing"
                >
                </v-text-field>

                <v-autocomplete
                    :readonly="!editing"
                    v-model="gift.recipientsUri"
                    :items="recipients"
                    item-text="name"
                    item-value="@id"
                    :search-input.sync="recipientsSearch"
                    @change="recipientsSearch = ''"
                    small-chips
                    deletable-chips
                    label="Destinataires"
                    multiple
                    auto-select-first
                    :menu-props="{ closeOnContentClick: true, closeOnClick: true }"
                ></v-autocomplete>
test3
                <v-text-field
                    v-model="gift.eventYear"
                    label="Année de l'évènement"
                    :readonly="!editing"
                >
                </v-text-field>

                <v-text-field
                    v-model="gift.price.value"
                    label="Prix"
                    :readonly="!editing"
                >
                </v-text-field>

                <v-textarea
                    v-model="gift.note"
                    label="Note"
                    :readonly="!editing"
                    rows="3"
                ></v-textarea>

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
        name: "GiftDetail",
        components: {
            FormSkeletonLoader,
        },
        data() {
            return {
                gift: {
                    price: {}
                },
                initialGift: {
                    price: {}
                },
                recipients: [],
                loading: false,
                recipientsSearch: '',
            };
        },
        created() {
            if (this.$route.meta.formMode === 'edit') {
                this.fetchGift(this.$route.params.id);
            }

            this.fetchRecipients();

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
            fetchGift(id) {
                this.loading = true;

                this.$http.get('/api/gifts/' + id)
                    .then( response => {
                        this.gift = response.data;
                        this.gift.recipientsUri = this.gift.recipients.map( element => element['@id'] );

                        this.initialGift = Object.assign({}, this.gift);
                        this.initialGift.price = Object.assign({}, this.gift.price);
                    })
                    .catch( error => {
                        if (error.response.status === 401) return;

                        error.status === 404
                            ? this.notify('error', "Le cadeau n'a pas été trouvé")
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
            onCancel()
            {
                this.gift = Object.assign({}, this.initialGift);
                this.gift.price = Object.assign({}, this.initialGift.price);

                if (this.$route.meta.formMode !== 'create') {
                    this.$store.state.editing = false;
                }

                this.$store.commit('formCanceled');
            },
            create()
            {
                const gift = this.gift;

                this.$http.post(
                    '/api/gifts',
                    JSON.stringify({
                        label: gift.label,
                        recipients: gift.recipientsUri,
                        eventYear: gift.eventYear,
                        price: {
                            value: parseFloat(gift.price.value)
                        },
                        note: gift.note,
                    }),
                )
                .then( () => {
                    this.notify('success', 'Le cadeau a bien été créé');
                    this.$store.commit('formValidated');

                    this.$router.push({ name: 'giftList' });
                })
                .catch( (error) => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Impossible de créer le cadeau');
                    this.$store.commit('formValidated', true);
                });
            },
            update()
            {
                const gift = this.gift;

                this.$http.put(
                    '/api/gifts/' + gift.id,
                    JSON.stringify({
                        label: gift.label,
                        recipients: gift.recipientsUri,
                        eventYear: gift.eventYear,
                        price: {
                            value: parseFloat(gift.price.value)
                        },
                        note: gift.note,
                    }),
                )
                .then( () => {
                    this.notify('success', 'Le cadeau a bien été modifié');
                    this.$store.commit('formValidated');
                })
                .catch( () => {
                    this.notify('error', 'Impossible de modifier le cadeau');
                    this.$store.commit('formValidated', true);
                });
            },
        }
    }

</script>

<style scoped>
    .form-reading >>> .v-input__slot::before {
        border-style: none;
    }
    .form-reading >>> .theme--light.v-label--is-disabled,
    .form-reading >>> .theme--light.v-input--is-disabled input,
    .form-reading >>> input[type="text"][disabled],
    .form-reading >>> textarea[disabled] {
        color: rgba(0, 0, 0, .87);
    }
    .form-reading >>> .theme--light.v-chip--disabled {
        opacity: 1;
    }
</style>
