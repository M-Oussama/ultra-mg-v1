import { defineStore } from 'pinia'
import axios from '@axios'

export const useClientImportStore = defineStore('ClientImportStore', {
  actions: {
    importCsv(file, departmentId) {
      const formData = new FormData()
      formData.append('file', file)
      if (departmentId !== undefined && departmentId !== null && `${departmentId}` !== '')
        formData.append('department_id', departmentId)

      return axios.post('/api/clients/import-csv', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    },
  },
})

