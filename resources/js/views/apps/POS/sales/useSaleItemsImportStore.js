import { defineStore } from 'pinia'
import axios from '@axios'

export const useSaleItemsImportStore = defineStore('SaleItemsImportStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/pos/sales/import-items-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

