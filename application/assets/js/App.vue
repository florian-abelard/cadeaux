
<template>

    <v-app>

        <v-app-bar color="teal darken-1" app dark>

            <v-app-bar-nav-icon @click.stop="showMenu = !showMenu">
            </v-app-bar-nav-icon>

            <v-toolbar-title>
                {{ displayTitle() }}
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn icon v-if="showBackButton()" @click="$router.go(-1)">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>

            <v-btn icon v-if="showEditButton()" @click="editing = true">
                <v-icon>mdi-square-edit-outline</v-icon>
            </v-btn>

            <v-btn icon v-if="showValidateButton()" @click="validateForm = true">
                <v-icon>mdi-check</v-icon>
            </v-btn>

            <v-btn icon v-if="showFilterButton()" @click.stop="showFilterDrawer = !showFilterDrawer">
                <v-icon>mdi-filter-outline</v-icon>
            </v-btn>

        </v-app-bar>

        <v-content>

            <v-navigation-drawer v-model="showMenu" temporary hide-overlay fixed left>

                <div class="drawer-container">

                    <v-list nav dense flat>

                        <v-list-item-group active-class="deep-green--text text--accent-4">

                            <router-link to="/" tag="v-list-item" link>
                                <v-list-item-icon>
                                    <v-icon>mdi-home</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Accueil</v-list-item-title>
                                </v-list-item-content>
                            </router-link>

                            <router-link to="/ideas" tag="v-list-item" link>
                                <v-list-item-icon>
                                    <v-icon>mdi-lightbulb-on-outline</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Idées cadeaux</v-list-item-title>
                                </v-list-item-content>
                            </router-link>

                            <router-link to="/gifts" tag="v-list-item" link>
                                <v-list-item-icon>
                                    <v-icon>mdi-gift-outline</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Cadeaux</v-list-item-title>
                                </v-list-item-content>
                            </router-link>

                            <router-link to="/events" tag="v-list-item" link>
                                <v-list-item-icon>
                                    <v-icon>mdi-calendar-star</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content>
                                    <v-list-item-title>Evénements</v-list-item-title>
                                </v-list-item-content>
                            </router-link>

                            <v-divider class="my-3"></v-divider>

                            <v-list-item link>
                                <v-list-item-icon>
                                    <v-icon>mdi-logout</v-icon>
                                </v-list-item-icon>
                                <v-list-item-content @click="logout">
                                    <v-list-item-title>Déconnexion</v-list-item-title>
                                </v-list-item-content>
                            </v-list-item>

                        </v-list-item-group>

                    </v-list>

                </div>

            </v-navigation-drawer>

            <v-container d-flex fluid>

                <router-view
                    :editing="editing"
                    :showFilterDrawer="showFilterDrawer"
                    :validateForm="validateForm"
                    v-on:authenticationSuccess="onAuthenticationSuccess"
                    v-on:formValidated="onFormValidated"
                    v-on:formCreated="onFormCreated"
                ></router-view>

            </v-container>

        </v-content>

        <notifications position="bottom center" width="300px"/>

    </v-app>

</template>

<script>

    export default {
        name: "App",
        data: () => ({
            authenticated: false,
            showMenu: false,
            showFilterDrawer: false,
            editing: false,
            validateForm: false
        }),
        mounted() {
            this.authenticated = window.authenticated;

            if (!this.authenticated && this.$route.name !== 'login') {
                this.$router.push({ name: 'login' });
            }
        },
        methods: {
            onAuthenticationSuccess() {
                this.authenticated = true;
            },
            onFormValidated(error = false) {
                if (!error) {
                    this.editing = false;
                }
                this.validateForm = false;
            },
            onFormCreated() {
                if (this.$route.meta.formMode === 'create') {
                    this.editing = true;
                } else {
                    this.editing = false;
                }
            },
            showBackButton() {
                return this.$route.meta.showBackButton;
            },
            showEditButton() {
                if (this.$route.meta.formMode === 'edit' && !this.editing) {
                    return true;
                }

                return false;
            },
            showValidateButton() {
                if (this.$route.meta.formMode === 'create') {
                    return true;
                }
                if (this.$route.meta.formMode === 'edit' && this.editing) {
                    return true;
                }

                return false;
            },
            showFilterButton() {
                return ['ideaList', 'giftList'].includes(this.$route.name);
            },
            displayTitle() {
                if (this.$route.name.startsWith('idea')) {
                    return 'Idées cadeaux';
                }
                if (this.$route.name.startsWith('gift')) {
                    return 'Cadeaux';
                }
            },
            logout()
            {
                this.$http.post(
                    '/api/logout',
                    '',
                    {
                        headers: {'Content-Type': 'application/json'},
                    }
                )
                .then( () => {
                    this.notify('success', 'Déconnexion réussie');

                    this.$router.push({ name: 'login' });
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Déconnexion impossible');
                });
            },
        },
    };

</script>

<style scoped>

    #app >>> .v-app-bar {
        z-index: 10 !important;
    }

    #app >>> .v-navigation-drawer {
        z-index: 4 !important;
    }

</style>
