<template>

    <v-container justify-center>

        <div v-if="!loading">
            <v-form
                ref="form"
                v-on:submit.prevent="onSubmit"
            >

                <v-text-field
                    v-model="user"
                    label="Identifiant"
                    required
                >
                </v-text-field>

                <v-text-field
                    type="password"
                    v-model="password"
                    label="Mot de passe"
                    required
                >
                </v-text-field>

                <v-container class="mt-3 d-flex justify-center" v-if="!editing">
                    <v-btn
                        type="submit"
                        medium
                    >
                        Valider
                    </v-btn>
                </v-container>

            </v-form>

        </div>

    </v-container>

</template>

<script>

    export default {
        name: "Login",
        props: {
            editing: false,
            submitForm: false
        },
        data() {
            return {
                user: '',
                password: '',
                loading: false,
            };
        },
        methods: {
            onSubmit()
            {
                this.$http.post(
                    '/api/login',
                    JSON.stringify({
                        user: this.user,
                        password: this.password,
                    }),
                    {
                        headers: {'Content-Type': 'application/json'},
                    }
                )
                .then( () => {
                    this.$emit('authenticationSuccess');
                    this.notify('success', 'Authentification réussie');

                    this.$router.push({ name: 'home' });
                })
                .catch( error => {
                    if (error.response.status === 401) return;

                    this.notify('error', 'Authentification impossible');
                });
            },
        }
    }

</script>

<style scoped>
</style>
