import { defineStore } from 'pinia'
import axios from '@axios'

export const useComponentStore = defineStore('useComponentStore', {
  actions: {
    // 👉 Fetch users data
    fetchData(params) { return axios.get('/api/components/list', { params }) },

    // 👉 Add
    addComponent(objectData) {
      const { name, asset_id } = objectData;
      return new Promise((resolve, reject) => {
        axios.post('/api/components/store', {
          name,
          asset_id,
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateComponent(objectData) {
      console.log(objectData)
      const { id, name, asset_id} = objectData;
      return new Promise((resolve, reject) => {
        let requestData = {
          name,
          asset_id,
        };
        axios.post('/api/components/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },



    // 👉 fetch single user
    deleteComponent(objectData) {
      const { id } = objectData
      return new Promise((resolve, reject) => {
        axios.delete('/api/components/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
