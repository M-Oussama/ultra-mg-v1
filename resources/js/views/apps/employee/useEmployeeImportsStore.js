import { defineStore } from 'pinia'
import axios from '@axios'

export const useEmployeeImportsStore = defineStore('EmployeeImportsStore', {
  actions: {
    importEmployeesCsv(file) {
      const formData = new FormData()
      formData.append('file', file)
      return axios.post('/api/employees/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },

    importEmployeeCareersCsv(file) {
      const formData = new FormData()
      formData.append('file', file)
      return axios.post('/api/employees/import-careers-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },

    importYearlyVacationsCsv(file) {
      const formData = new FormData()
      formData.append('file', file)
      return axios.post('/api/employees/vacation/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

