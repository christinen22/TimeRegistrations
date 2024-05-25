<template>
    <div>
        <router-link to="/profile">Tillbaka till profil</router-link>
        <h2>Ändra lösenord</h2>
        <form @submit.prevent="changePassword">
            <input
                type="password"
                v-model="oldPassword"
                placeholder="Nuvarande lösenord"
                required
            />
            <input
                type="password"
                v-model="newPassword"
                placeholder="Nytt lösenord"
                required
            />
            <input
                type="password"
                v-model="passwordConfirmation"
                placeholder="Bekräfta nytt lösenord"
                required
            />
            <button type="submit">Ändra lösenord</button>
        </form>
        <p v-if="error">{{ error }}</p>
        <p v-if="message">{{ message }}</p>
    </div>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            userData: {
                name: "",
                email: "",
            },
            oldPassword: "",
            newPassword: "",
            passwordConfirmation: "",
            error: null,
            message: null,
        };
    },
    methods: {
        async updateProfile() {
            try {
                const token = localStorage.getItem("token");
                if (!token) {
                    throw new Error("No token found");
                }

                await axios.put(
                    "http://127.0.0.1:8000/api/user",
                    this.userData,
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                this.message = "Profile updated successfully";
                this.error = null;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to update profile";
                this.message = null;
            }
        },
        async changePassword() {
            try {
                const token = localStorage.getItem("token");
                if (!token) {
                    throw new Error("No token found");
                }

                await axios.put(
                    "http://127.0.0.1:8000/api/user/password",
                    {
                        current_password: this.oldPassword,
                        password: this.newPassword,
                        password_confirmation: this.passwordConfirmation,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                this.message = "Lösenordet ändrat";
                this.error = null;
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to change password";
                this.message = null;
            }
        },
    },
    async mounted() {
        try {
            const token = localStorage.getItem("token");
            if (!token) {
                throw new Error("No token found");
            }

            const response = await axios.get("http://127.0.0.1:8000/api/user", {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
            this.userData = response.data.user;
        } catch (error) {
            this.error =
                error.response?.data?.message || "Failed to fetch user data";
        }
    },
};
</script>

<style>
/* Add your styles here */
</style>
