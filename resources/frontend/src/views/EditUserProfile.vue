<template>
    <div>
        <h2>Edit Profile</h2>
        <form @submit.prevent="updateProfile">
            <input
                type="text"
                v-model="userData.name"
                placeholder="Name"
                required
            />
            <input
                type="email"
                v-model="userData.email"
                placeholder="Email"
                required
            />
            <button type="submit">Save Changes</button>
        </form>
        <form @submit.prevent="changePassword">
            <input
                type="password"
                v-model="oldPassword"
                placeholder="Current Password"
                required
            />
            <input
                type="password"
                v-model="newPassword"
                placeholder="New Password"
                required
            />
            <button type="submit">Change Password</button>
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
                        oldPassword: this.oldPassword,
                        newPassword: this.newPassword,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                this.message = "Password changed successfully";
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
            this.userData = response.data;
        } catch (error) {
            this.error =
                error.response?.data?.message || "Failed to fetch user data";
        }
    },
};
</script>
