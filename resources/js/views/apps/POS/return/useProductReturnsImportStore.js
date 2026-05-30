import { defineStore } from 'pinia'
import axios from '@axios'

export const useProductReturnsImportStore = defineStore('ProductReturnsImportStore', {
  actions: {
    importReturnsCsv(file, departmentId) {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('department_id', departmentId)

      return axios.post('/api/returns/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },

    importReturnListsCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/returns/import-lists-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})
