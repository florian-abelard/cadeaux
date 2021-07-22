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
                    :disabled="!editing"
                >
                </v-text-field>

                <v-autocomplete
                    v-model="gift.recipientsUri"
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
                    v-model="gift.eventYear"
                    label="Année de l'évènement"
                    :disabled="!editing"
                >
                </v-text-field>

                <v-text-field
                    v-model="gift.price.value"
                    label="Prix"
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

    import FormSkeletonLoader from '../../components/loaders/FormSkeletonLoader.vue'

    export default {
        name: "GiftDetail",
        props: {
            editing: false,
            submitForm: false
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
            submitForm: function () {
                if (this.submitForm) {
                    this.onSubmit();
                    this.$emit('formValidated');
                }
            }
        },
        methods: {
            fetchGift(id) {
                this.loading = true;

                fetch('/api/gifts/' + id)
                    .then( response => {
                        if (!response.ok) throw response;
                        return response.json();
                    })
                    .then( (data) => {
                        this.gift = data;
                        this.gift.recipientsUri = this.gift.recipients.map( element => element['@id'] );
                    })
                    .catch( (error) => {
                        if (error.status === 404) {
                            this.notify('error', "Le cadeau n'a pas été trouvé");
                        } else {
                            this.notify('error', error.statusText);
                        }
                        this.$router.push({ name: 'home' });
                    })
                    .finally( () => {
                        this.loading = false;
                    })
                ;
            },
            fetchRecipients()
            {
                fetch('/api/recipients')
                .then( response => {
                    if (!response.ok) throw response;
                    return response.json();
                })
                .then( (data) => {
                    this.recipients = data['hydra:member'];
                })
                .catch( (error) => {
                    console.log(error);
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

                fetch('/api/gifts', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/ld+json'},
                    body: JSON.stringify({
                        label: gift.label,
                        recipients: gift.recipientsUri,
                        eventYear: gift.eventYear,
                        price: {
                            value: parseFloat(gift.price)
                        },
                    })
                })
                .then( response => {
                    if (!response.ok) throw response;
                    this.notify('success', 'Le cadeau a bien été créé');
                })
                .catch( (error) => {
                    console.log(error);
                    this.notify('error', 'Impossible de créer le cadeau');
                });
            },
            update()
            {
                const gift = this.gift;

                fetch('/api/gifts/' + gift.id, {
                        method: 'PUT',
                        headers: {'Content-Type': 'application/ld+json'},
                        body: JSON.stringify({
                            label: gift.label,
                            recipients: gift.recipientsUri,
                            eventYear: gift.eventYear,
                            price: {
                                value: parseFloat(gift.price.value)
                            },
                        })
                    })
                    .then( response => {
                        if (!response.ok) throw response;
                        this.notify('success', 'Le cadeau a bien été modifié');
                    })
                .catch( (error) => {
                    console.log(error);
                    this.notify('error', 'Impossible de modifier le cadeau');
                });
                ;
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
