<template>
    <div class="edit-profile-container">
        <router-link to="/profile" class="back-link"
            >Tillbaka till profil</router-link
        >
        <h2>Ändra lösenord</h2>
        <form @submit.prevent="changePassword" class="password-form">
            <input
                type="password"
                v-model="oldPassword"
                placeholder="Nuvarande lösenord"
                required
                class="input-field"
            />
            <input
                type="password"
                v-model="newPassword"
                placeholder="Nytt lösenord"
                required
                class="input-field"
            />
            <input
                type="password"
                v-model="passwordConfirmation"
                placeholder="Bekräfta nytt lösenord"
                required
                class="input-field"
            />
            <button type="submit" class="save-button">Ändra lösenord</button>
        </form>
        <p v-if="error" class="error-message">{{ error }}</p>
        <p v-if="message" class="success-message">{{ message }}</p>
    </div>
</template>

<script lang="ts">
import { defineComponent, ref } from "vue";
import { changeUserPassword } from "../api/userService";

export default defineComponent({
    name: "EditUserProfile",
    setup() {
        const oldPassword = ref("");
        const newPassword = ref("");
        const passwordConfirmation = ref("");
        const error = ref<string | null>(null);
        const message = ref<string | null>(null);

        const changePassword = async () => {
            try {
                const token = localStorage.getItem("token");
                if (!token) {
                    throw new Error("No token found");
                }

                await changeUserPassword(token, {
                    current_password: oldPassword.value,
                    password: newPassword.value,
                    password_confirmation: passwordConfirmation.value,
                });
                message.value = "Lösenordet ändrat";
                error.value = null;
            } catch (err: any) {
                error.value =
                    err.response?.data?.message || "Failed to change password";
                message.value = null;
            }
        };

        return {
            oldPassword,
            newPassword,
            passwordConfirmation,
            error,
            message,
            changePassword,
        };
    },
});
</script>

<style>
.edit-profile-container {
    background-color: #2c2c2c;
    color: #e0e0e0;
    padding: 30px 40px;
    border-radius: 20px;
    max-width: 600px;
    margin: 100px auto;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    text-align: center;
}

.back-link {
    display: block;
    margin-bottom: 20px;
    color: #007bff;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}

h2 {
    margin-bottom: 20px;
    color: #ffffff;
    font-size: 2rem;
}

.password-form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.input-field {
    width: calc(100% - 20px);
    padding: 10px;
    margin-bottom: 10px;
    border: none;
    border-radius: 20px;
    background-color: #616161;
    color: white;
    font-size: 16px;
}

.save-button {
    padding: 15px 20px;
    border: none;
    border-radius: 20px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    font-size: 16px;
    width: 100%;
}

.save-button:hover {
    background-color: #0069d9;
}

.error-message,
.success-message {
    margin-top: 20px;
    font-size: 1.2rem;
}

.error-message {
    color: #f44336;
}

.success-message {
    color: #4caf50;
}
</style>
