import { defineStore } from 'pinia'
import axios from '@axios'

export const useMaintenanceStore = defineStore('useMaintenanceStore', {
  actions: {
    // 👉 Fetch users data
    fetchData(params) { return axios.get('/api/maintenances/list', {headers: {'Authorization': localStorage.getItem('accessToken')}}
      )},

    getComponents(asset_id) { return axios.get('/api/assets/'+asset_id+'/components') },

    // 👉 Add
    addMaintenance(objectData) {
      return new Promise((resolve, reject) => {

        var {maintenance} = objectData
        var {name, component_id, asset_id, maintenance_date, next_maintenance_date, notes,technician_assigned_id, status} = maintenance
        axios.post('/api/maintenances/store', {name, component_id, asset_id, maintenance_date, next_maintenance_date, notes,technician_assigned_id, status}, {headers: {'Authorization': localStorage.getItem('accessToken')}}).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateComponent(objectData) {

      return new Promise((resolve, reject) => {
        var {maintenance} = objectData
        var {id, name, component_id, asset_id, maintenance_date, next_maintenance_date, notes, technician_assigned_id, status} = maintenance
        axios.post('/api/maintenances/update/' + id, {name, component_id, asset_id, maintenance_date, next_maintenance_date, notes, technician_assigned_id, status}, {headers: {'Authorization': localStorage.getItem('accessToken')}})
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },



    // 👉 fetch single user
    deleteMaintenance(objectData) {
      const { id } = objectData
      return new Promise((resolve, reject) => {
        axios.delete('/api/maintenances/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },


  },
})
