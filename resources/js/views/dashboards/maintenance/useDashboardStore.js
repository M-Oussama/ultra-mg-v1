import { defineStore } from 'pinia'
import axios from '@axios'

export const useDashboardStore = defineStore('DashboardStore', {
  actions: {
    // 👉 Fetch all project
    getRecentMaintenances(params) {
      return axios.get('/api/dashboard/maintenances/recent', {params})
    },
    getIncomingMaintenances(params) {
      return axios.get('/api/dashboard/maintenances/incoming', {params})
    },
    getMaintenanceDashboard() {
      return axios.get('/api/dashboard/maintenances')
    },
  },
})
