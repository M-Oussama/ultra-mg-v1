import { defineStore } from 'pinia'
import axios from '@axios'

export const useCertifyProductStore = defineStore('CertifyProductStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/certify-products/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

