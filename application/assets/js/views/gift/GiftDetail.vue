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
                    :menu-props="{ closeOnContentClick: true }"
                ></v-autocomplete>

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
        props: {
            editing: false,
            validateForm: false
        },
        components: {
            FormSkeletonLoader,
        },
        data() {
            return {
                gift: {
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
        watch: {
            validateForm: function () {
                if (this.validateForm) {
                    this.onSubmit();
                }
            }
        },
        methods: {
            fetchGift(id) {
                this.loading = true;

                this.$http.get('/api/gifts/' + id)
                    .then( response => {
                        this.gift = response.data;
                        this.gift.recipientsUri = this.gift.recipients.map( element => element['@id'] );
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
                    this.$emit('formValidated');

                    this.$router.push({ name: 'giftList' });
                })
                .catch( (error) => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Impossible de créer le cadeau');
                    this.$emit('formValidated', true);
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
                    this.$emit('formValidated');
                })
                .catch( () => {
                    this.notify('error', 'Impossible de modifier le cadeau');
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
