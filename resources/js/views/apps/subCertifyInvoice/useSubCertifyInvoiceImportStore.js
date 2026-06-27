import { defineStore } from 'pinia'
import axios from '@axios'

export const useSubCertifyInvoiceImportStore = defineStore('SubCertifyInvoiceImportStore', {
  actions: {
    importCsv(file, clientId) {
      const formData = new FormData()

      formData.append('file', file)
      formData.append('client_id', clientId)

      return axios.post('/api/sub-certify-invoices/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

