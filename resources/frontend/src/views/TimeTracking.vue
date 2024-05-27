<template>
    <div class="container">
        <div>
            <h1>Tidsregistrering</h1>
            <p v-if="isClockedIn" class="clocked-in-indicator">
                <i class="fas fa-clock"></i> Stämplat in
                {{ formatTime(currentSessionStart) }}
                <span v-if="isClockedIn">| {{ elapsedTime }}</span>
            </p>
        </div>

        <div class="buttons">
            <button
                class="btn clock-in"
                @click="clockIn"
                :disabled="isClockedIn"
            >
                Stämpla in
            </button>
            <button
                class="btn clock-out"
                @click="clockOut"
                :disabled="!isClockedIn"
            >
                Stämpla ut
            </button>
        </div>
        <p v-if="loading" class="status">Loading...</p>
        <p v-if="error" class="error">Error: {{ error }}</p>
        <ul class="time-trackings">
            <li
                v-for="tracking in timeTrackings.slice().reverse()"
                :key="tracking.id"
                class="tracking-item"
            >
                <div class="tracking-row">
                    <span class="icon"><i class="fas fa-sign-in-alt"></i></span>
                    <strong>Stämplat in: </strong>
                    <span class="time">{{ formatTime(tracking.time_in) }}</span>
                </div>
                <div class="tracking-row">
                    <span class="icon"
                        ><i class="fas fa-sign-out-alt"></i
                    ></span>
                    <strong>Stämplat ut: </strong>
                    <span class="time"
                        >{{
                            tracking.time_out
                                ? formatTime(tracking.time_out)
                                : "Aktiv"
                        }}
                    </span>
                </div>
                <div class="tracking-row">
                    <span class="icon"><i class="fas fa-clock"></i></span>
                    <strong>Total tid:</strong>
                    <span class="time">
                        {{
                            calculateElapsedTime(
                                tracking.time_in,
                                tracking.time_out
                            )
                        }}
                    </span>
                </div>
                <div class="tracking-row">
                    <span class="icon"
                        ><i class="fas fa-map-marker-alt"></i
                    ></span>
                    <strong>GPS:</strong>
                    <span class="time">
                        {{ tracking.gps_coordinates }}
                    </span>
                </div>
                <button @click="editTimeTracking(tracking)" class="btn edit">
                    Ändra
                </button>
            </li>
        </ul>

        <div v-if="editModalVisible" class="modal">
            <h2>Ändra tidsregistrering</h2>
            <form @submit.prevent="updateTimeTracking">
                <label for="editTimeIn">Stämplat in:</label>
                <input
                    type="datetime-local"
                    id="editTimeIn"
                    v-model="editedTimeTracking.time_in"
                    required
                />
                <label for="editTimeOut">Stämplat ut:</label>
                <input
                    type="datetime-local"
                    id="editTimeOut"
                    v-model="editedTimeTracking.time_out"
                />
                <label for="editGPS">GPS Koordinater:</label>
                <input
                    type="text"
                    id="editGPS"
                    v-model="editedTimeTracking.gps_coordinates"
                    required
                />
                <button type="submit" class="btn save">Spara ändringar</button>
                <button @click="cancelEdit" class="btn cancel">Avbryt</button>
                <button
                    type="button"
                    @click="deleteTimeTracking(editedTimeTracking.id)"
                    class="btn delete"
                >
                    Radera
                </button>
            </form>
        </div>
    </div>
</template>

<script>
import moment from "moment"; //tidsformatering
import {
    fetchTimeTrackings,
    clockIn,
    clockOut,
    updateTimeTracking,
    deleteTimeTracking,
} from "../api/timeTrackingService";

export default {
    data() {
        return {
            timeTrackings: [],
            loading: false,
            error: null,
            editModalVisible: false,
            editedTimeTracking: {
                id: null,
                time_in: null,
                time_out: null,
                gps_coordinates: null,
            },
            isClockedIn: false,
            currentSessionStart: null,
            elapsedTime: null,
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
                const response = await fetchTimeTrackings(token);
                this.timeTrackings = response.data.time_trackers;
                this.checkCurrentSession();
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
                const currentTime = moment().format("YYYY-MM-DDTHH:mm");

                const response = await clockIn(token, {
                    time_in: currentTime,
                    gps_coordinates: gpsCoordinates,
                });
                this.timeTrackings.push(response.data.time_tracker);
                this.updateUserStatus("At Work");
                this.isClockedIn = true;
                this.currentSessionStart = currentTime;
                this.startTimer();
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
                const currentTime = moment().format("YYYY-MM-DDTHH:mm");

                const response = await clockOut(token, latestTracking.id, {
                    time_in: moment(latestTracking.time_in).format(
                        "YYYY-MM-DDTHH:mm"
                    ),
                    time_out: currentTime,
                    gps_coordinates: latestTracking.gps_coordinates,
                });
                latestTracking.time_out = response.data.time_tracker.time_out;
                this.updateUserStatus("Off Work");
                this.isClockedIn = false;
                this.elapsedTime = this.calculateElapsedTime(
                    this.currentSessionStart,
                    currentTime
                );
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to clock out";
            }
        },
        editTimeTracking(tracking) {
            this.editedTimeTracking = {
                ...tracking,
                time_in: moment(tracking.time_in).format("YYYY-MM-DDTHH:mm"),
                time_out: tracking.time_out
                    ? moment(tracking.time_out).format("YYYY-MM-DDTHH:mm")
                    : null,
            };
            this.editModalVisible = true;
        },
        async updateTimeTracking() {
            try {
                const token = localStorage.getItem("token");
                const formattedTimeIn = moment(
                    this.editedTimeTracking.time_in
                ).format("YYYY-MM-DDTHH:mm");
                const formattedTimeOut = this.editedTimeTracking.time_out
                    ? moment(this.editedTimeTracking.time_out).format(
                          "YYYY-MM-DDTHH:mm"
                      )
                    : null;

                const response = await updateTimeTracking(
                    token,
                    this.editedTimeTracking.id,
                    {
                        time_in: formattedTimeIn,
                        time_out: formattedTimeOut,
                        gps_coordinates:
                            this.editedTimeTracking.gps_coordinates,
                    }
                );

                const updatedTrackingIndex = this.timeTrackings.findIndex(
                    (tracking) => tracking.id === this.editedTimeTracking.id
                );
                if (updatedTrackingIndex !== -1) {
                    this.timeTrackings.splice(
                        updatedTrackingIndex,
                        1,
                        response.data.time_tracker
                    );
                }
                this.editModalVisible = false;
            } catch (error) {
                console.error(error);
                this.error =
                    error.response?.data?.message ||
                    "Failed to update time tracking";
            }
        },
        async deleteTimeTracking(id) {
            try {
                const token = localStorage.getItem("token");
                await deleteTimeTracking(token, id);
                this.timeTrackings = this.timeTrackings.filter(
                    (tracking) => tracking.id !== id
                );
                this.editModalVisible = false;
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Failed to delete time tracking";
            }
        },
        cancelEdit() {
            this.editModalVisible = false;
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
        updateUserStatus(status) {
            this.userStatus = status;
        },
        checkCurrentSession() {
            const latestTracking =
                this.timeTrackings[this.timeTrackings.length - 1];
            if (latestTracking && !latestTracking.time_out) {
                this.isClockedIn = true;
                this.currentSessionStart = latestTracking.time_in;
                this.startTimer();
            }
        },
        startTimer() {
            setInterval(() => {
                if (this.isClockedIn) {
                    this.elapsedTime = this.calculateElapsedTime(
                        this.currentSessionStart,
                        moment().toISOString()
                    );
                }
            }, 1000);
        },
        calculateElapsedTime(start, end) {
            const startTime = moment(start);
            const endTime = end ? moment(end) : moment();
            const duration = moment.duration(endTime.diff(startTime));
            const hours = Math.floor(duration.asHours());
            const minutes = Math.floor(duration.asMinutes()) % 60;
            return `${hours} hr ${minutes} min`;
        },
        formatTime(time) {
            return moment(time).format("YYYY-MM-DD HH:mm");
        },
    },
};
</script>

<style>
.container {
    background-color: #2c2c2c;
    color: #e0e0e0;
    padding: 30px 40px;
    border-radius: 20px;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

h2 {
    margin-bottom: 20px;
    text-align: center;
    color: #ffffff;
    font-size: 24px;
}

.buttons {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    align-items: center;
}

.btn {
    padding: 15px;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
}

.clock-in {
    background-color: #007bff;
    color: white;
    margin-right: 10px;
}

.clock-out {
    background-color: #f44336;
    color: white;
}

.clocked-in-indicator {
    text-align: center;
    font-size: 1.5rem;
    font-weight: bold;
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.status,
.error {
    text-align: center;
    margin-top: 20px;
}

.time-trackings {
    list-style: none;
    padding-top: 20px;
    justify-content: center;
    display: flex;
    flex-wrap: wrap;
}

.tracking-item {
    background-color: #3c3c3c;
    padding: 20px;
    margin: 10px;
    border-radius: 10px;
}

.tracking-row {
    display: flex;
    align-items: center;
    margin: 5px 0;
}

.icon {
    margin-right: 10px;
}

.time {
    margin-left: 5px;
}
.edit {
    background-color: #2196f3;
    color: white;
    border-radius: 10px;
    padding: 10px 15px;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
}

.modal {
    background-color: #424242;
    padding: 20px;
    border-radius: 20px;
    position: fixed;
    top: 50px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
}

label {
    display: block;
    margin: 10px 0 5px;
}

input[type="datetime-local"],
input[type="text"] {
    width: calc(100% - 20px);
    padding: 10px;
    margin: 5px 0 15px;
    border: none;
    border-radius: 20px;
    background-color: #616161;
    color: white;
    font-size: 16px;
}

.save {
    background-color: #4caf50;
    color: white;
    border-radius: 20px;
    padding: 15px;
    font-size: 16px;
    cursor: pointer;
    display: block;
    width: 100%;
    margin-top: 10px;
}

.cancel {
    background-color: #f44336;
    color: white;
    border-radius: 20px;
    padding: 15px;
    font-size: 16px;
    cursor: pointer;
    display: block;
    width: 100%;
    margin-top: 10px;
}

.delete {
    background-color: #ff5722;
    color: white;
    border-radius: 20px;
    padding: 15px;
    font-size: 16px;
    cursor: pointer;
    display: block;
    width: 100%;
    margin-top: 10px;
}
</style>
