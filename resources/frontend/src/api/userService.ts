import apiClient from "./axios";

export const fetchUserData = (token: string) => {
    return apiClient.get('/user', {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const updateUserData = (token: string, data: any) => {
    return apiClient.put('/user', data, {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const changeUserPassword = (token: string, data: any) => {
    return apiClient.put('/user/password', data, {
        headers: { Authorization: `Bearer ${token}` }
    });
};

export const deleteUser = (token: string) => {
    return apiClient.delete('/user', {
        headers: { Authorization: `Bearer ${token}` }
    });
};
