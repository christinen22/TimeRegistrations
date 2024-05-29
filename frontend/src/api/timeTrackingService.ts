import apiClient from "./axios";


interface TimeTrackingData {
    time_in: string;
    time_out?: string;
    gps_coordinates: string;
}

export const fetchTimeTrackings = (token: string) => {
    return apiClient.get('/time-trackings', {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const clockIn = (token: string, data: TimeTrackingData) => {
    return apiClient.post('/time-trackings', data, {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const clockOut = (token: string, id: number, data: Partial<TimeTrackingData>) => {
    return apiClient.put(`/time-trackings/${id}`, data, {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const updateTimeTracking = (token: string, id: number, data: TimeTrackingData) => {
    return apiClient.put(`/time-trackings/${id}`, data, {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const deleteTimeTracking = (token: string, id: number) => {
    return apiClient.delete(`/time-trackings/${id}`, {
        headers: { Authorization: `Bearer ${token}` }
    });
};
