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
                fetch('/api/login', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        user: this.user,
                        password: this.password,
                    })
                })
                .then( response => {
                    if (!response.ok) throw response;

                    this.$emit('authenticationSuccess');
                    this.notify('success', 'Authentification réussie');
                })
                .catch( (error) => {
                    console.log(error);

                    this.notify('error', 'Authentification impossible');
                });
            },
        }
    }

</script>

<style scoped>
</style>
