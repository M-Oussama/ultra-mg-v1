import { defineStore } from 'pinia'
import axios from '@axios'

export const useVacationStore = defineStore('VacationStore', {
  actions: {
    // 👉 Fetch  data

    fetchListRecord(id) { return axios.get('/api/employees/vacation/list/'+id) },

    // 👉 Add
    addNew(startDate, endDate, id, count) {

      return new Promise((resolve, reject) => {
        axios.post('/api/employees/vacation/store/'+id,{startDate, endDate, count})
            .then(response => resolve(response))
            .catch(error => reject(error));
      })
    },

    updateVacation(startDate, endDate, id, count) {

      return new Promise((resolve, reject) => {
        axios.post('/api/employees/vacation/update/'+id,{startDate, endDate, count})
            .then(response => resolve(response))
            .catch(error => reject(error));
      })
    },

    // 👉 Update
    update(params) {
 
      const data = params.employee.value;
      return new Promise((resolve, reject) => {
        let requestData = {
          data
        };
        axios.post('/api/employees/vacation/update/' + data.id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchRecord(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/employees/vacation/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
    // 👉 delete single
    deleteVacation(id) {


      return new Promise((resolve, reject) => {
        axios.delete('/api/employees/vacation/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
