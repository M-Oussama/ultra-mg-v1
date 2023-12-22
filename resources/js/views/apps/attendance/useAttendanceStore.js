import { defineStore } from 'pinia'
import axios from '@axios'

export const useAttendanceStore = defineStore('AttendanceStore', {
  actions: {
    // 👉 Fetch users data
    fetchAttendances(params) { return axios.get('/api/attendances/list', { params }) },

    getAttendanceByID(id) { return axios.get('/api/attendances/getAttendanceByID/'+id, { id }) },

    fetchAttendanceData(params) {  return axios.get('/api/attendances/getAttendanceData/'+params) },

    // 👉 Add User
    addAttendance(attendanceData) {

      const attendance = attendanceData.attendance.value;
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/store', {
          attendance
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Add User
    submitAttendance(attendance, attendanceId) {

      var employees =  attendance.value.employees;
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/submit', {
          attendanceId,
          employees
        }).then(response => resolve(response))
            .catch(error => reject(error))
      })
    },

    updateAttendance(employeeAttendance, attendanceId) {
      var attendances =  employeeAttendance.value;
      console.log(attendances)
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/update', {
          attendanceId,
          attendances
        }).then(response => resolve(response))
            .catch(error => reject(error))
      })
    },

    AddEmployeeToAttendance(employee, attendanceId) {
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/AddEmployeeToAttendance', {
          attendanceId,
          employee
        }).then(response => resolve(response))
            .catch(error => reject(error))
      })
    },
    RemoveEmployeeFromAttendance(attendance, attendanceId) {
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/RemoveEmployeeFromAttendance', {
          attendanceId,
          attendance
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
        axios.get(`/apps/employees/${id}`).then(response => resolve(response)).catch(error => reject(error))
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
