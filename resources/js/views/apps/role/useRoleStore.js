import { defineStore } from 'pinia'
import axios from '@axios'

export const useRoleStore = defineStore('RoleStore', {
  actions: {
    // 👉 Fetch users data
    fetchData(params) { return axios.get('/api/roles/list', { params }) },

    // 👉 Add
    addRole(objectData) {
      const { role, permissions } = objectData;
      return new Promise((resolve, reject) => {
        axios.post('/api/roles/store', {
          role,
          permissions
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateRole(objectData) {
      console.log(objectData)
      const { id, role,permissions} = objectData;
      return new Promise((resolve, reject) => {
        let requestData = {
          role,
          permissions
        };
        axios.post('/api/roles/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },



    // 👉 fetch single user
    deleteRole(objectData) {
      const { id } = objectData
      return new Promise((resolve, reject) => {
        axios.delete('/api/roles/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
