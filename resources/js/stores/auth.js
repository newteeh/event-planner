import { defineStore } from 'pinia';
import apiClient from '../api/axios';
export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: JSON.parse(localStorage.getItem('user')) || null,
        token: localStorage.getItem('token') || null
    }),
    getters:{
        isAuthenticated: (state) => !!state.token 
    },
    actions: {
        async login(credentials){
            try{
                const response = await apiClient.post('/login', credentials);
                this.token = response.data.token;
                this.user = response.data.user;

                localStorage.setItem('token', response.data.token);
                localStorage.setItem('user', JSON.stringify(response.data.user));

                return {success: true};
            }
            catch (error){
                this.token = null;
                this.user = null;

                localStorage.removeItem('token');
                localStorage.removeItem('user');

                return{
                    success: false,
                    errors: error.response?.data?.errors || {email: ['Ошибка входа']}
                };
            }
            
        },

        async logout(){
            try{
                await apiClient.post('/logout');
            }
            finally{
                this.token = null;
                this.user = null;

                localStorage.removeItem('token');
                localStorage.removeItem('user');
            }
            }
    }
});