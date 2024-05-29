<template>
    <div class="user-profile-container">
        <h1 v-if="user" class="welcome-message">
            Välkommen, {{ user.personnummer }}
        </h1>
        <p v-if="loading" class="loading-message">Loading...</p>
        <p v-if="error" class="error-message">Error: {{ error }}</p>
        <div class="links">
            <router-link to="/profile/edit" class="btn change-password-link">
                Ändra lösenord
            </router-link>
            <button @click="logout" class="btn logout-button">Logga ut</button>
        </div>
    </div>
    <TimeTracking />
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { fetchUserData } from "../api/userService";
import TimeTracking from "./TimeTracking.vue";

export default defineComponent({
    name: "UserProfile",
    components: {
        TimeTracking,
    },
    setup() {
        const user = ref(null);
        const loading = ref(true);
        const error = ref<string | null>(null);
        const router = useRouter();

        const fetchUser = async () => {
            try {
                const token = localStorage.getItem("token");
                if (!token) {
                    throw new Error("No token found");
                }

                const response = await fetchUserData(token);
                user.value = response.data.user;
                loading.value = false;
            } catch (err: any) {
                error.value = err.message || "Failed to fetch user data";
                loading.value = false;
            }
        };

        const logout = () => {
            localStorage.removeItem("token");
            router.push("/");
        };

        onMounted(fetchUser);

        return {
            user,
            loading,
            error,
            logout,
        };
    },
});
</script>

<style>
.user-profile-container {
    background-color: #2c2c2c;
    color: #e0e0e0;
    border-radius: 20px;
    margin-bottom: 10rem;
    font-family: Arial, sans-serif;
    text-align: center;
    display: flex;
    flex-direction: column;
}

.welcome-message {
    margin: 0;
    color: #ffffff;
    font-size: 2.5rem;
}

.loading-message,
.error-message {
    text-align: center;
    margin-top: 20px;
    color: #ff6f61;
}

.links {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    align-items: center;
}

.btn {
    display: inline-block;
    padding: 15px 20px;
    border: none;
    border-radius: 20px;
    background-color: #007bff;
    color: white;
    text-decoration: none;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
}

.change-password-link,
.time-tracking-link {
    margin-right: 10px;
}

.btn:hover {
    background-color: #0069d9;
}

.logout-button {
    background-color: #f44336;
}

.logout-button:hover {
    background-color: #d32f2f;
}
</style>
