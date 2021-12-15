<template>

  <v-dialog
      v-model="show"
      max-width="300"
      >
      <v-form
          ref="giftDialogForm"
          v-on:submit.prevent="validate"
      >
          <v-card>
              <v-card-title class="headline">
                  Création du cadeau
              </v-card-title>

              <v-card-text>
                  <p>Choisis le ou les destinataire(s) du cadeau. L'idée cadeau sera mise à jour automatiquement. <br/>
                  Si plus aucun destinataire n'est associé à l'idée cadeau, elle sera supprimée.</p>
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
                  ></v-autocomplete>

                  <v-text-field
                      v-model="gift.eventYear"
                      label="Année de l'évènement"
                  >
                  </v-text-field>
              </v-card-text>

              <v-card-actions>
                  <v-spacer></v-spacer>

                  <v-btn
                      color="darken-1"
                      text
                      @click="show = false"
                  >
                      Annuler
                  </v-btn>

                  <v-btn
                      color="darken-1"
                      text
                      type="submit"
                  >
                      Valider
                  </v-btn>
              </v-card-actions>
          </v-card>
      </v-form>
  </v-dialog>

</template>

<script>

    export default {
        props: {
            value: Boolean,
            ideaRecipientsUri: {},
            recipients: Array,
        },
        data() {
            return {
                gift: {
                    recipientsUri: [],
                    eventYear: ''
                },
            };
        },
        created() {
            this.gift.recipientsUri = this.ideaRecipientsUri;
        },
        watch: {
            ideaRecipientsUri: {
                handler(value) {
                    this.gift.recipientsUri = value;
                },
            },
        },
        computed: {
            show: {
                get () {
                    return this.value;
                },
                set (value) {
                    this.$emit('input', value);
                }
            }
        },
        methods: {
            validate() {
                this.$emit('giftFromIdeaValidated', this.gift);
            },
        }
    }

</script>
