import { defineStore } from 'pinia'
import axios from '@axios'

export const useEmployeeStore = defineStore('EmployeeStore', {
  actions: {
    // 👉 Fetch users data
    fetchEmployees(params) { return axios.get('/api/employees/list', { params }) },

    // 👉 Add User
    addEmployee(employeeData) {

      const employee = employeeData.employee.value;
      return new Promise((resolve, reject) => {
        axios.post('/api/employees/store', {
          employee
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Update User
    updateEmployee(employeeData) {
 
      const employee = employeeData.employee.value;
      return new Promise((resolve, reject) => {
        let requestData = {
          employee
        };
        axios.post('/api/employees/update/' + employee.id, requestData)
            .then(response => resolve(response))
            .catch(error => reject(error));
      });
    },


    fetchEmployee(id) {
      return new Promise((resolve, reject) => {
        axios.get(`/api/employees/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
    // 👉 delete single client
    deleteEmployee(employeeData) {

      const { id } = employeeData
      return new Promise((resolve, reject) => {
        axios.delete('/api/employees/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
