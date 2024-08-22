import { defineStore } from 'pinia'
import axios from '@axios'

export const useDashboardStore = defineStore('DashboardStore', {
  actions: {
    // 👉 Fetch all project
    getEmployeeInVacation(params) {
      return axios.get('/api/dashboard/vacations', {params})
    },
    getIncomingVacations(params) {
      return axios.get('/api/dashboard/incoming-vacations', {params})
    },
    getAdminDashboard() {
      return axios.get('/api/dashboard/admin')
    },
  },
})
