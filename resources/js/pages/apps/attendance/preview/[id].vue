<script setup>
import AddNewEmployeeDrawer from '@/views/apps/employee/list/AddNewEmployeeDrawer.vue'
import EditEmployeeDrawer from '@/views/apps/employee/list/EditEmployeeDrawer.vue';
import ConfirmationDialog from '@/views/apps/employee/list/ConfirmationDialog.vue';
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {useAttendanceStore} from "@/views/apps/attendance/useAttendanceStore";

const attendanceStore = useAttendanceStore()
const searchQuery = ref('')
const route = useRoute()
const loading = ref(false)
const loading2 = ref({
  isActive:true
})
const currentTab = ref(0)
const items = [
  'foo',
  'bar',
  'fizz',
  'buzz',
]

const values = ref([])
const isPreview = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalAttendances = ref(0)
let attendance = ref()
let _attendance = ref({
  month:'',
  year:''
})
let employeeAttendance = ref([])
let attendances = ref({})
// 👉 Fetching Employees
const getAttendanceByID = () => {
  loading2.value.isActive = true;
  attendanceStore.getAttendanceByID(Number(route.params.id)).then(response => {

     employeeAttendance.value = response.data.employeeAttendance
     _attendance.value = response.data.attendance


    console.log(response)
     loading2.value.isActive = false;
  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;
  })
}

watchEffect(getAttendanceByID)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchEmployees
watch(searchQuery, () => {

  //loading.value = true;
  fetchAttendances();
});


const isAddNewEmployeeDrawerVisible = ref(false)
const isEditEmployeeDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedEmployee = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = attendances.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = attendances.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalAttendances.value } entries`
})

const saveAttendance = () => {
  loading2.value.isActive = true;
  attendanceStore.submitAttendance(attendance, route.params.id).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  });


}

const updateEmployee = attendanceData => {
  loading2.value.isActive = true;
  attendanceStore.updateAttendance(attendanceData).then(response =>{
    successMiddleware(response.data.message)

    loading2.value.isActive = false;
    // refetch User
    fetchAttendances()
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })


}

const deleteEmployee = attendanceData =>  {
  loading2.value.isActive = true;
  attendanceStore.deleteAttendance(attendanceData).then(response =>{
    loading2.value.isActive = false;
    successMiddleware(response.data.message)
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })

  // refetch Employee
  fetchAttendances()
}

const openUpdateDrawer = (employee) => {
  isEditEmployeeDrawerVisible.value = true;
  selectedEmployee = employee;
}

const openConfirmationDialog = (employee) => {
  isDialogVisible.value = true;
  selectedEmployee = employee;

}
const toggleCheckboxThree = ref(true)
const capitalizedLabel = label => {
  const convertLabelText = label.toString()

  return ''
}

const AllDaysPressed = employee =>{
console.log(employee.allDays);

  for (let i = 0; i <employee.workingDays.length ; i++) {
    employee.workingDays[i].present = !employee.allDays;
  }
  console.log(employee);
}
const printAttendance = () => {
  window.print()
}
const getStartDate = (array) =>{
  return array[""] ? array[""][0].career.start_date : array[0][0].career.start_date;

}
const getEndDate = (array) =>{
  return array[""] ? (array[""][0].career.end_date == null ? '' : array[""][0].career.end_date) : (array[0][0].career.start_date == null ? '' : array[0][0].career.end_date);

}
</script>

<template>
  <section>
    <VRow>
      <v-overlay
        :model-value="loading2.isActive"
        class="align-center justify-center"
      >
        <v-progress-circular
          color="primary"
          indeterminate
          size="64"
        ></v-progress-circular>
      </v-overlay>
      <VCol  cols="12"
             md="9">
        <VCard  class="text-center" >


          <VCardText class=" flex-wrap justify-space-between flex-column flex-sm-row print-row">

            <h3>Pointage Mois {{('0'+ _attendance.month).slice(-2)}}/ {{_attendance.year}}</h3>
            <div class="align-center flex-wrap gap-4 ">
              <!-- 👉 Search  -->
              <div >
                <v-progress-circular
                  v-if="loading"
                  indeterminate
                  color="primary"
                ></v-progress-circular>

              </div>

              <v-table class="mt-6">
                <thead>
                <tr >
                  <th class="text-center">
                    Employee
                  </th>

                  <th class="text-center" >
                    Date de récrutement
                  </th>
                  <th class="text-center" >
                    Jours
                  </th>
                  <th class="text-center" >
                    Jours de travail
                  </th>

                  <th class="text-center" >
                    Date de fin
                  </th>
                </tr>
                </thead>
                <tbody>

                <tr v-for="employee in employeeAttendance">
                  <th class="text-center">
                    {{employee.employee.name}} {{employee.employee.surname}}
                  </th>
                  <th class="text-center" >
                    <p class="ma-0 " >{{getStartDate(employee.result)}}</p>

                  </th>
                  <th class="text-center" >
                      Jours(J)
                  </th>
                  <th class="text-center" >
                    {{employee.present_count}}
                  </th>
                  <th class="text-center" >
                    <p class="ma-0 " >{{getEndDate(employee.result)}}</p>
                  </th>
                </tr>
                </tbody>
              </v-table>



            </div>
          </VCardText>


        </VCard>
      </VCol>

      <VCol
        cols="12"
        md="3"
        class="d-print-none"
      >
        <VCard class="mb-8 print-row" >
          <VCardText>
            <!-- 👉 Send Invoice -->
            <VBtn
              block
              prepend-icon="tabler-send"
              class="mb-2"
            >
              Send Invoice
            </VBtn>

            <!-- 👉 Save -->
            <v-btn
              block
              color="default"
              variant="tonal"
              @click="printAttendance"
            >
              Print
            </v-btn>

          </VCardText>
        </VCard>
      </VCol>
    </VRow>

    <!-- 👉 Add New Employee -->
    <AddNewEmployeeDrawer
      v-model:isDrawerOpen="isAddNewEmployeeDrawerVisible"
      @employee-data="addNewEmployee"
    />

    <!-- 👉 Edit Employee -->
    <EditEmployeeDrawer
      v-model:isDrawerOpen="isEditEmployeeDrawerVisible"
      v-model:employee = "selectedEmployee"
      @employee-data="updateEmployee"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:employee="selectedEmployee"
      @employee-data="deleteEmployee"
      v-if="isDialogVisible"
      />

  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 31.6rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
.rotate{
  transform: rotate(-90deg);width: 1px;padding: 10px
}
@media print {
  .v-application {
    background: none !important;
  }

  @page { margin: 0; size: auto; }

  .layout-page-content,
  .v-row,
  .v-col-md-9 {
    padding: 0;
    margin: 0;
  }

  .product-buy-now {
    display: none;
  }

  .v-navigation-drawer,
  .layout-vertical-nav,
  .app-customizer-toggler,
  .layout-footer,
  .layout-navbar,
  .layout-navbar-and-nav-container {
    display: none;
  }

  .v-col-md-6 {
    flex: 0 0 50% !important;
    max-width: 50% !important;
  }
  .v-col-md-5 {
    flex: 0 0 41.6666666667% !important;
    max-width: 41.6666666667% !important;
  }
  .v-col-md-7 {
    flex: 0 0 58.3333333333% !important;
    max-width: 58.3333333333% !important;
  }
  .v-col-md-4 {
    flex: 0 0 33.3333333333%;
    max-width: 33.3333333333%;
  }

  .v-card {
    box-shadow: none !important;

    .print-row {
      flex-direction: row !important;
    }
  }

  .layout-content-wrapper {
    padding-inline-start: 0 !important;
  }
  .text-color-black{
    color: black !important;
  }

  .invoiceGeneral{
    background-color: white !important;
    border-radius: 10px !important;
    padding: 0px !important;
    margin: 17px !important;
  }
  .display-center{
    display: inline-grid
  }

  .custom-border{
    padding: 10px 0 10px 15px;

    background-color: #f3f2f4;
    border-radius: 19px;
    margin-right: 6px;
    margin-bottom: 6px;
  }
  .custom-white-border{
    padding: 10px 0 10px 15px;

    background-color: white;
    border-radius: 19px;
    margin-right: 12px;
    border: 1px solid #e4e4e4;
    margin-bottom: 6px;
  }
}
</style>
<route lang="yaml">
meta:
  action: list
  subject: attendances
</route>
