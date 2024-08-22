import { defineStore } from 'pinia'
import axios from '@axios'

export const usePermissionStore = defineStore('PermissionStore', {
  actions: {
    // 👉 Fetch users data
    fetchData(params) { return axios.get('/api/permissions/list', { params }) },

    // 👉 Add
    addPermission(objectData) {
      const { action, subject } = objectData;
      return new Promise((resolve, reject) => {
        axios.post('/api/permissions/store', {
          action,
          subject
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updatePermission(objectData) {
      const { id, action, subject} = objectData;
      return new Promise((resolve, reject) => {
        let requestData = {
          action, subject,
        };
        axios.post('/api/permissions/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },



    // 👉 fetch single user
    deletePermission(objectData) {
      const { id } = objectData
      return new Promise((resolve, reject) => {
        axios.delete('/api/permissions/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
