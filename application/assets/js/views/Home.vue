<template>

    <v-container d-flex justify-center>

        <v-row justify="space-between">

            <v-col cols="12" md="4">
                <router-link to="/ideas" v-slot="{ href, route, navigate }">
                    <v-card :href="href" @click="navigate" color="teal lighten-4">
                        <v-container>
                            <v-row no-gutters justify="space-between" align="center">
                                <v-col cols="10">
                                    <v-card-title class="headline">Idées cadeaux</v-card-title>
                                </v-col>
                                <v-col cols="2">
                                    <v-icon size="36">mdi-lightbulb-on-outline</v-icon>
                                </v-col>
                            </v-row>
                            <v-row no-gutters>
                                <v-col cols="12">
                                    <v-card-subtitle>
                                        Gérez toutes vos idées cadeaux.<br/>
                                        Associez vos idées à un ou plusieurs proches... puis transformez-les en cadeaux !
                                    </v-card-subtitle>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </router-link>
            </v-col>

            <v-col cols="12" md="4">
                <router-link to="/gifts" v-slot="{ href, route, navigate }">
                    <v-card :href="href" @click="navigate" color="teal lighten-4">
                        <v-container>
                            <v-row no-gutters justify="space-between" align="center">
                                <v-col cols="10">
                                    <v-card-title class="headline">Cadeaux</v-card-title>
                                </v-col>
                                <v-col cols="2">
                                    <v-icon size="36">mdi-gift-outline</v-icon>
                                </v-col>
                            </v-row>
                            <v-row no-gutters>
                                <v-col cols="12">
                                    <v-card-subtitle>Affichez vos cadeaux effectués... Triez-les par année, ou famille...</v-card-subtitle>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </router-link>
            </v-col>

            <v-col cols="12" md="4">
                <router-link to="/events" v-slot="{ href, route, navigate }">
                    <v-card :href="href" @click="navigate" color="teal lighten-4">
                        <v-container>
                            <v-row no-gutters justify="space-between" align="center">
                                <v-col cols="10">
                                    <v-card-title class="headline">Evénements</v-card-title>
                                </v-col>
                                <v-col cols="2">
                                    <v-icon size="36">mdi-calendar-star</v-icon>
                                </v-col>
                            </v-row>
                            <v-row no-gutters>
                                <v-col cols="12">
                                    <v-card-subtitle>
                                        Créez des événements, ajoutez des participants et vérifiez que tout le monde a bien son cadeau !
                                    </v-card-subtitle>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </router-link>
            </v-col>

        </v-row>

    </v-container>

</template>

<script>
    export default {
        name: "Home"
    };
</script>
