<template>
    <div>
        <h1>Time Tracking</h1>
        <button @click="clockIn">Clock In</button>
        <button @click="clockOut">Clock Out</button>
        <p v-if="loading">Loading...</p>
        <p v-if="error">Error: {{ error }}</p>
        <ul>
            <li v-for="tracking in timeTrackings" :key="tracking.id">
                Time In: {{ tracking.time_in }} | Time Out:
                {{ tracking.time_out ? tracking.time_out : "N/A" }} | GPS:
                {{ tracking.gps_coordinates }}
            </li>
        </ul>
    </div>
</template>

<script>
import apiClient from "../axios";

export default {
    data() {
        return {
            timeTrackings: [],
            loading: false,
            error: null,
        };
    },
    mounted() {
        this.fetchTimeTrackings();
    },
    methods: {
        async fetchTimeTrackings() {
            this.loading = true;
            try {
                const token = localStorage.getItem("token");
                const response = await apiClient.get("/time-trackings", {
                    headers: {
                        Authorization: `Bearer ${token}`,
                    },
                });
                this.timeTrackings = response.data.time_trackers;
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to fetch time trackings";
            } finally {
                this.loading = false;
            }
        },
        async clockIn() {
            try {
                const token = localStorage.getItem("token");
                const gpsCoordinates = await this.getGPS();
                const response = await apiClient.post(
                    "/time-trackings",
                    {
                        time_in: new Date().toISOString(),
                        gps_coordinates: gpsCoordinates,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                this.timeTrackings.push(response.data.time_tracker);
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to clock in";
            }
        },
        async clockOut() {
            try {
                const token = localStorage.getItem("token");
                const latestTracking =
                    this.timeTrackings[this.timeTrackings.length - 1];
                if (!latestTracking || latestTracking.time_out) {
                    this.error = "No active time tracking entry to clock out";
                    return;
                }
                const response = await apiClient.put(
                    `/time-trackings/${latestTracking.id}`,
                    {
                        time_out: new Date().toISOString(),
                        gps_coordinates: latestTracking.gps_coordinates,
                    },
                    {
                        headers: {
                            Authorization: `Bearer ${token}`,
                        },
                    }
                );
                latestTracking.time_out = response.data.time_tracker.time_out;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to clock out";
            }
        },
        async getGPS() {
            return new Promise((resolve, reject) => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            resolve(
                                `${position.coords.latitude},${position.coords.longitude}`
                            );
                        },
                        () => {
                            reject("Unable to retrieve GPS coordinates");
                        }
                    );
                } else {
                    reject("Geolocation is not supported by this browser");
                }
            });
        },
    },
};
</script>

<style>
/* Add your styles here */
</style>
