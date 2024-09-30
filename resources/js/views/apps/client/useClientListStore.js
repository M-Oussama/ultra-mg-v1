import { defineStore } from 'pinia'
import axios from '@axios'

export const useClientListStore = defineStore('ClientListStore', {
  actions: {
    // 👉 Fetch users data
    fetchClients(params) { return axios.get('/api/clients/list', { params }) },

    // 👉 Add User
    addClient(clientData) {
      const { name, surname, email, address, phone, NRC, NIF, NIS, NART, city_id} = clientData;
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
          NIS,
          city_id
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateClient(clientData) {
      const { id, name, surname, email, address, phone, NRC, NIF, NIS, NART, city_id} = clientData;
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
          NIS,
          city_id
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

    fetchClientLog(params) { return axios.post('/api/clients/log', { params }) },

    exportAllLog(params) { return axios.post('/api/clients/log/generate', { params }) },

    getLogList(client_id) {
      return axios.get('/api/client-log/' + client_id+'/download',{
        responseType: 'blob'
      })
    },

    getLogListWithReturn(client_id) {
      return axios.get('/api/client-log/return/' + client_id+'/download',{
        responseType: 'blob'
      })
    },
    getProductLog(client_id) {
      return axios.get('/api/client/'+ client_id +'/product/log/download',{
        responseType: 'blob'
      })
    }

  },
})
