import { defineStore } from 'pinia'
import axios from '@axios'

export const useChequeImportStore = defineStore('ChequeImportStore', {
  actions: {
    importCsv(file) {
      const formData = new FormData()
      formData.append('file', file)

      return axios.post('/api/cheques/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

