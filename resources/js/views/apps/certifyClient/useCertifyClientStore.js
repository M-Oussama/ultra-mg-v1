import { defineStore } from 'pinia'
import axios from '@axios'

export const useCertifyClientStore = defineStore('CertifyClientStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/certify-clients/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

