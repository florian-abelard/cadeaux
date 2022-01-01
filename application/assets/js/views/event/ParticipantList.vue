<template>
    <v-container class="pa-0">

        <v-container v-if="!loading" justify-center class="pa-0">

            Participants

            <v-list>
                <template v-for="(participant, index) in participants">

                    <v-list-item :key="participant.id">

                        <v-list-item-content>

                            <v-list-item-title v-text="participant.name"></v-list-item-title>

                        </v-list-item-content>

                        <v-list-item-action>

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

    </v-container>
</template>

<script>

    export default {
        name: "ParticipantList",
        props: {
            participants: Array,
            editing: Boolean,
        },
        data() {
            return {
                loading: false,
            };
        },
        methods: {
            deleteParticipant(participant) {
                this.$emit('participantDeleted', participant);
            },
        }
    }

</script>

<style scoped>
</style>
