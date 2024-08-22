import { defineStore } from 'pinia'
import axios from '@axios'

export const useAssetStore = defineStore('useAssetStore', {
  actions: {
    // 👉 Fetch users data
    fetchData(params) { return axios.get('/api/assets/list', { params }) },

    // 👉 Add
    addAsset(objectData) {
      const { name } = objectData;
      return new Promise((resolve, reject) => {
        axios.post('/api/assets/store', {
          name,
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateAsset(objectData) {
      console.log(objectData)
      const { id, name} = objectData;
      return new Promise((resolve, reject) => {
        let requestData = {
          name,
        };
        axios.post('/api/assets/update/' + id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },



    // 👉 fetch single user
    deleteAsset(objectData) {
      const { id } = objectData
      return new Promise((resolve, reject) => {
        axios.delete('/api/assets/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
