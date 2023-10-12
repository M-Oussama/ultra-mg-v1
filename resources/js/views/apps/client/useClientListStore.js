import { defineStore } from 'pinia'
import axios from '@axios'

export const useClientListStore = defineStore('ClientListStore', {
  actions: {
    // 👉 Fetch users data
    fetchClients(params) { return axios.get('/api/clients/list', { params }) },

    // 👉 Add User
    addClient(clientData) {
      const { name, surname, email, address, phone, NRC, NIF, NIS, NART} = clientData;
      return new Promise((resolve, reject) => {
        axios.post('/api/clients/store', {
          name,
          surname,
          email,
          address,
          phone,
          NRC,
          NIF,
          NART,
          NIS
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateClient(clientData) {
      const { id, name, surname, email, address, phone, NRC, NIF, NIS, NART} = clientData;
      return new Promise((resolve, reject) => {
        let requestData = {
          name,
          surname,
          email,
          address,
          phone,
          NRC,
          NIF,
          NART,
          NIS
        };
        axios.post('/api/clients/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchClient(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/apps/clients/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
    // 👉 delete single client
    deleteClient(clientData) {
      console.log(clientData)
      const { id } = clientData
      return new Promise((resolve, reject) => {
        axios.delete('/api/clients/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
