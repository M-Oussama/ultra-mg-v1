import { defineStore } from 'pinia'
import axios from '@axios'

export const useCertifyInvoiceImportStore = defineStore('CertifyInvoiceImportStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/certifyInvoices/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

