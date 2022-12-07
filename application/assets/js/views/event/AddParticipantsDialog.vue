<template>

    <v-dialog
        v-model="show"
        max-width="300"
        >
        <v-form
            v-on:submit.prevent="validate"
        >
            <v-card>
                <v-card-title class="headline">
                    Ajouter des participants
                </v-card-title>

                <v-card-text>
                    <p>Vous pouvez sélectionner toutes les personnes d'un groupe ou seulement quelques destinataires.</p>

                    <v-autocomplete
                        v-model="groupsToAdd"
                        :items="groups"
                        item-text="label"
                        return-object
                        :search-input.sync="groupsSearch"
                        @change="groupsSearch = ''"
                        small-chips
                        deletable-chips
                        label="Groupes"
                        multiple
                        auto-select-first
                        :menu-props="{ closeOnContentClick: true, closeOnClick: true }"
                    ></v-autocomplete>

                    <v-autocomplete
                        v-model="participantsToAdd"
                        :items="recipients"
                        item-text="name"
                        return-object
                        :search-input.sync="recipientsSearch"
                        @change="recipientsSearch = ''"
                        small-chips
                        deletable-chips
                        label="Participants"
                        multiple
                        auto-select-first
                        :menu-props="{ closeOnContentClick: true, closeOnClick: true }"
                    ></v-autocomplete>
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
        },
        data() {
            return {
                groupsToAdd: [],
                participantsToAdd: [],
                groups: [],
                recipients: [],
                groupsSearch: '',
                recipientsSearch: '',
            };
        },
        created() {
            this.fetchGroups();
            this.fetchRecipients();
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
            fetchGroups()
            {
                this.$http.get('/api/groups?groups[]=group:read&groups[]=group:read:members')
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
            validate() {
                const participantsToAdd = [
                    ...this.getParticipantsFromGroups(this.groupsToAdd),
                    ...this.participantsToAdd,
                ];
                this.participantsToAdd = [];
                this.groupsToAdd = [];

                this.$emit('participantsAdded', participantsToAdd);
            },
            getParticipantsFromGroups(groups) {
                return groups
                    .map(group => group.members)
                    .flat()
                ;
            },
        }
    }

</script>
