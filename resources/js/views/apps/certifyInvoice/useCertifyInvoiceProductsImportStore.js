import { defineStore } from 'pinia'
import axios from '@axios'

export const useCertifyInvoiceProductsImportStore = defineStore('CertifyInvoiceProductsImportStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/certifyInvoices/import-products-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

