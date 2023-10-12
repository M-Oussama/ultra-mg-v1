import { defineStore } from 'pinia'
import axios from '@axios'

export const useUserListStore = defineStore('UserListStore', {
  actions: {
    // 👉 Fetch users data
    fetchUsers(params) { return axios.get('/api/users/list', { params }) },

    // 👉 Add User
    addUser(userData) {
      const { name, email, password } = userData;
      return new Promise((resolve, reject) => {
        axios.post('/api/users/store', {
          name,
          email,
          password,
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateUser(userData) {
      const { id, name, email, password, passwordChanged } = userData;
      return new Promise((resolve, reject) => {
        let requestData = {
          passwordChanged,
          name,
          email,
        };

        // Add password to the request data only if passwordChanged is true
        if (passwordChanged) {
          requestData.password = password;
        }

        axios.post('/api/users/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchUser(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/apps/users/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
    // 👉 fetch single user
    deleteUser(userData) {
      console.log(userData)
      const { id } = userData
      return new Promise((resolve, reject) => {
        axios.delete('/api/users/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
