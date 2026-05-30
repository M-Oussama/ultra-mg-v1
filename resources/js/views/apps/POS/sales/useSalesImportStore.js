import { defineStore } from 'pinia'
import axios from '@axios'

export const useSalesImportStore = defineStore('SalesImportStore', {
  actions: {
    importCsv(file, departmentId) {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('department_id', departmentId)

      return axios.post('/api/pos/sales/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

