<template>
    <v-container class="pa-0">

        <v-container v-if="!loading" justify-center class="pa-0">

            <div class="participant-list-header" justify="space-between">
                <span>Participants</span>

                <v-btn
                    class="mx-2"
                    fab
                    small
                    dark
                    color="teal darken-1"
                    v-if="editing"
                    v-on:click="openAddParticipantsDialog()"
                >
                    <v-icon dark>mdi-plus</v-icon>
                </v-btn>
            </div>

            <v-list dense>
                <template v-for="(participant, index) in participants">

                    <v-list-item :key="participant.id">

                        <v-list-item-content>

                            <v-list-item-title v-text="participant.name"></v-list-item-title>

                        </v-list-item-content>

                        <v-list-item-action>

                            <v-btn
                                icon
                                small
                                v-if="!editing"
                                :disabled="participant.ideas.length === 0"
                                v-on:click="gotoIdeas(participant)"
                            >
                                <v-icon color="lighten-1">mdi-lightbulb-on-outline</v-icon>
                            </v-btn>

                            <v-btn
                                icon
                                small
                                v-if="!editing"
                                :disabled="participant.gifts.length === 0"
                                v-on:click="gotoGifts(participant)"
                            >
                                <v-icon color="lighten-1">mdi-gift-outline</v-icon>
                            </v-btn>

                            <v-btn
                                icon
                                small
                                v-if="editing"
                                v-on:click="deleteParticipant(participant)"
                            >
                                <v-icon color="grey lighten-1">mdi-delete</v-icon>
                            </v-btn>

                        </v-list-item-action>

                    </v-list-item>

                    <v-divider v-if="index + 1 < participants.length" :key="index"></v-divider>
                </template>
            </v-list>

        </v-container>

        <add-participants-dialog
            v-model="showAddParticipantsDialog"
            v-on:participantsAdded="addParticipants"
        />

    </v-container>
</template>

<script>

    import AddParticipantsDialog from './AddParticipantsDialog.vue';

    export default {
        components: { AddParticipantsDialog },
        name: "ParticipantList",
        props: {
            participants: Array,
            editing: Boolean,
        },
        data() {
            return {
                loading: false,
                showAddParticipantsDialog: false,
            };
        },
        methods: {
            deleteParticipant(participantToDelete) {
                this.$emit('participantDeleted', participantToDelete);
            },
            addParticipants(participantsToAdd) {
                this.showAddParticipantsDialog = false
                this.$emit('participantsAdded', participantsToAdd);
            },
            openAddParticipantsDialog()
            {
                this.showAddParticipantsDialog = true;
            },
            gotoIdeas(participant)
            {
                console.log('goToIdeas');

            },
            gotoGifts(participant)
            {
                console.log('gotoGifts');
            },
        }
    }

</script>

<style scoped>

    .participant-list-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

</style>
