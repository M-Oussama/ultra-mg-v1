import { defineStore } from 'pinia'
import axios from '@axios'

export const useSubCertifyInvoiceProductsImportStore = defineStore('SubCertifyInvoiceProductsImportStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()

      formData.append('file', file)

      return axios.post('/api/sub-certify-invoices/import-products-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

