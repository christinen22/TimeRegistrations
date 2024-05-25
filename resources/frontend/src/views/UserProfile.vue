<template>
    <div>
        <h1 v-if="user">Välkommen, {{ user.personnummer }}</h1>
        <p v-if="loading">Loading...</p>
        <p v-if="error">Error: {{ error }}</p>
        <router-link to="/profile/edit">Ändra lösenord</router-link>
        <router-link to="/time-tracking">Tidsregistrering</router-link>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "UserProfile",
    data() {
        return {
            user: null,
            loading: true,
            error: null,
        };
    },
    mounted() {
        this.fetchUserData();
    },
    methods: {
        async fetchUserData() {
            try {
                const token = localStorage.getItem("token");
                if (!token) {
                    throw new Error("No token found");
                }

                const response = await axios.get(
                    "http://127.0.0.1:8000/api/user",
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                console.log(response.data.user.personnummer);
                this.user = response.data.user;
                this.loading = false;
            } catch (error) {
                this.error = error.message;
                this.loading = false;
            }
        },
    },
};
</script>

<style>
/* lägg till styles sen */
</style>
