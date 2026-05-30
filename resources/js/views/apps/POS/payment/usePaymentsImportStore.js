import { defineStore } from 'pinia'
import axios from '@axios'

export const usePaymentsImportStore = defineStore('PaymentsImportStore', {
  actions: {
    importPaymentsCsv(file, departmentId) {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('department_id', departmentId)

      return axios.post('/api/pos/payments/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },

    importPartialPaymentsCsv(file, departmentId) {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('department_id', departmentId)

      return axios.post('/api/pos/partial-payments/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

