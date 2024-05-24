<template>
    <div>
        <h2>Login</h2>
        <form @submit.prevent="login">
            <input
                type="text"
                v-model="personnummer"
                placeholder="Personnummer"
                required
            />
            <input
                type="password"
                v-model="password"
                placeholder="Password"
                required
            />
            <button type="submit">Login</button>
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
                // Store the token in local storage
                localStorage.setItem("token", token);
                // Redirect the user to the profile page
                this.$router.push("/profile");
            } catch (error) {
                // Handle login error
                console.error("Failed to login:", error);
                alert(
                    "Failed to login. Please check your credentials and try again."
                );
            }
        },
    },
};
</script>
