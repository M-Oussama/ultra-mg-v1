import{bc as u,a1 as o}from"./main.3e4fe580.js";const y=u("AttendanceStore",{actions:{fetchAttendances(e){return o.get("/api/attendances/list",{params:e})},getAttendanceByID(e){return o.get("/api/attendances/getAttendanceByID/"+e,{id:e})},fetchAttendanceData(e){return o.get("/api/attendances/getAttendanceData/"+e)},deleteCareer(e){return o.get("/api/attendances/career/delete/"+e)},addAttendance(e){const n=e.attendance.value;return new Promise((c,a)=>{o.post("/api/attendances/store",{attendance:n}).then(t=>c(t)).catch(t=>a(t))})},submitAttendance(e,n){var c=e.value.employees;return new Promise((a,t)=>{o.post("/api/attendances/submit",{attendanceId:n,employees:c}).then(r=>a(r)).catch(r=>t(r))})},updateAttendance(e,n){var c=e.value;return console.log(c),new Promise((a,t)=>{o.post("/api/attendances/update",{attendanceId:n,attendances:c}).then(r=>a(r)).catch(r=>t(r))})},AddEmployeeToAttendance(e,n){return new Promise((c,a)=>{o.post("/api/attendances/AddEmployeeToAttendance",{attendanceId:n,employee:e}).then(t=>c(t)).catch(t=>a(t))})},RemoveEmployeeFromAttendance(e,n){return new Promise((c,a)=>{o.post("/api/attendances/RemoveEmployeeFromAttendance",{attendanceId:n,attendance:e}).then(t=>c(t)).catch(t=>a(t))})},updateEmployee(e){const n=e.employee.value;return new Promise((c,a)=>{let t={employee:n};o.post("/api/employees/update/"+n.id,t).then(r=>c(r)).catch(r=>a(r))})},fetchEmployee(e){return new Promise((n,c)=>{o.get(`/apps/employees/${e}`).then(a=>n(a)).catch(a=>c(a))})},generateEmail(e,n){return console.log("email"),console.log(n),o.post(`/api/recrutement/generateEmail/${e}`,{email:n})},deleteEmployee(e){const{id:n}=e;return new Promise((c,a)=>{o.delete("/api/employees/delete/"+n).then(t=>c(t)).catch(t=>a(t))})},fetchEmployeesByAttendance(e){return o.get("/api/attendances/employees/list/"+e)},updateEndDate(e,n,c,a,t,r,d){return new Promise((p,m)=>{o.post("/api/attendances/updateEndDate/"+c,{endDate:e,startDate:n,position:a,real_start_date:t,real_end_date:r,position_ar:d}).then(s=>p(s)).catch(s=>m(s))})},addEmployeeAttendanceRecord(e,n,c,a,t,r,d,p,m){return new Promise((s,i)=>{o.post("/api/attendances/addNewEmployeeAttendanceRecord/"+c,{endDate:e,startDate:n,position:a,birth_certificate:t,national_card:r,real_start_date:d,real_end_date:p,position_ar:m}).then(l=>s(l)).catch(l=>i(l))})}}});export{y as u};