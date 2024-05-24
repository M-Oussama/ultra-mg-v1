import { defineStore } from 'pinia'
import axios from '@axios'

export const useAttendanceStore = defineStore('AttendanceStore', {
  actions: {
    // 👉 Fetch users data
    fetchAttendances(params) { return axios.get('/api/attendances/list', { params }) },

    getAttendanceByID(id) { return axios.get('/api/attendances/getAttendanceByID/'+id, { id }) },

    fetchAttendanceData(params) {  return axios.get('/api/attendances/getAttendanceData/'+params) },

    deleteCareer(params) {  return axios.get('/api/attendances/career/delete/'+params) },

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
    generateEmail(id, email) {

      console.log("email");
      console.log(email);
      return axios.post(`/api/recrutement/generateEmail/${id}`, { email });
    },
    // 👉 delete single client
    deleteEmployee(employeeData) {

      const { id } = employeeData
      return new Promise((resolve, reject) => {
        axios.delete('/api/employees/delete/'+ id).then(response => resolve(response)).catch(error => reject(error))
      })
    },


    fetchEmployeesByAttendance(id){
      return axios.get('/api/attendances/employees/list/'+id)
    },
    updateEndDate(endDate, startDate, id, position, real_start_date, real_end_date){
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/updateEndDate/'+id,{endDate, startDate, position, real_start_date, real_end_date})
            .then(response => resolve(response))
            .catch(error => reject(error));
      })
    },
    addEmployeeAttendanceRecord(endDate, startDate, id,position, birth_certificate, national_card, real_start_date, real_end_date){
     console.log("start date");
     console.log(startDate);
      return new Promise((resolve, reject) => {
        axios.post('/api/attendances/addNewEmployeeAttendanceRecord/'+id,{endDate, startDate, position, birth_certificate, national_card, real_start_date, real_end_date})
            .then(response => resolve(response))
            .catch(error => reject(error));
      })
    }
  },
})

