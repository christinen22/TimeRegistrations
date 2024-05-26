<template>
    <div class="login-container">
        <h2>Personalsida</h2>
        <form @submit.prevent="login" class="login-form">
            <input
                type="personnummer"
                v-model="personnummer"
                placeholder="Personnummer"
                required
                class="input-field"
            />
            <input
                type="password"
                v-model="password"
                placeholder="Lösenord"
                required
                class="input-field"
            />
            <button type="submit" class="login-button">Logga in</button>
        </form>
    </div>
</template>

<script>
import axios from "axios";

export default {
    name: "LogIn",
    data() {
        return {
            personnummer: "",
            password: "",
        };
    },
    methods: {
        async login() {
            try {
                const response = await axios.post(
                    "http://127.0.0.1:8000/api/login",
                    {
                        personnummer: this.personnummer,
                        password: this.password,
                    }
                );

                const token = response.data.token;
                localStorage.setItem("token", token);
                this.$router.push("/profile");
            } catch (error) {
                console.error("Failed to login:", error);
                alert(
                    "Failed to login. Please check your credentials and try again."
                );
            }
        },
    },
};
</script>

<style>
.login-container {
    background-color: #2c2c2c;
    color: #e0e0e0;
    padding: 30px 40px;
    border-radius: 20px;
    max-width: 400px;
    margin: 100px auto;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

h2 {
    margin-bottom: 20px;
    text-align: center;
    color: #ffffff;
    font-size: 24px;
}

.login-form {
    margin-top: 20px;
}

.input-field {
    width: 100%;
    padding: 15px;
    margin-bottom: 15px;
    border: none;
    border-radius: 20px;
    background-color: #404040;
    color: white;
    font-size: 16px;
    box-sizing: border-box;
    display: block;
}

.login-button {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 20px;
    background-color: #007bff;
    color: white;
    font-size: 16px;
    cursor: pointer;
    display: block;
}

.login-button:hover {
    background-color: #0056b3;
}
</style>
