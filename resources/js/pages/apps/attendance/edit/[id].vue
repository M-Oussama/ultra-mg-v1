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
const isPreview = ref(false)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalAttendances = ref(0)
let attendance = ref({
  employees:[],
  dates:[],
})
let employeeAttendance = ref({})
let attendances = ref({})
let employees = ref([])
let employee = ref({})
let removEmp = ref({})
// 👉 Fetching Employees
const fetchAttendances = () => {
  loading2.value.isActive = true;
  attendanceStore.getAttendanceByID(Number(route.params.id)).then(response => {
    employeeAttendance.value = response.data.employeeAttendance
    employees.value = response.data.employees

    console.log("employeeAttendance")
    console.log(employeeAttendance)
     loading2.value.isActive = false;
  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;
  })
}

watchEffect(fetchAttendances)

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
  attendanceStore.updateAttendance(attendance, route.params.id).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
    router.push('/apps/attendance/preview/'+route.params.id)
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  });


}

const updateAttendance = () => {
  loading2.value.isActive = true;

  attendanceStore.updateAttendance(employeeAttendance,Number(route.params.id)).then(response =>{
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

const printName = employee =>{


  return employee.name === undefined ? 'Select An Employee' : employee.name + ' '+employee.surname;
}

const AddEmployeeToAttendance = () =>{
  loading2.value.isActive = true;

  attendanceStore.AddEmployeeToAttendance(employee.value,Number(route.params.id)).then(response =>{
    loading2.value.isActive = false;
    employees.value = response.data.employees

    employeeAttendance[response.data.key] = response.data.newAttendance;

    console.log(employeeAttendance)

    // refetch User
    fetchAttendances()
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })
}
const updateAll = attendance => {
  attendance.allDays = !attendance.allDays;

  for (let i = 0; i <attendance['result'].length ; i++) {
      attendance['result'][i].present = attendance.allDays;

  }
}
const removeEmployee = attendance => {
 loading2.value.isActive = true;

 console.log(attendance.result)
  attendanceStore.RemoveEmployeeFromAttendance(attendance.result,Number(route.params.id)).then(response =>{
    loading2.value.isActive = false;
    employees = response.data.employees

  if (employeeAttendance.value.hasOwnProperty(attendance.id)) {
    delete employeeAttendance.value[attendance.id];
  }

    // refetch User
    fetchAttendances()
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })
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
      <VCol cols="9">
        <VCard title="New Attendance">
          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">



            <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div >
                <v-progress-circular
                  v-if="loading"
                  indeterminate
                  color="primary"
                ></v-progress-circular>

              </div>





            </div>
          </VCardText>
          <div class="pa-6">
            <VRow>
              <VCol cols="8">
                <VAutocomplete
                  v-model="employee"
                  :items="employees"
                  label="Employee"
                  item-value="id"
                  :item-title="printName"
                />
              </VCol>
              <VCol cols="3">
                <v-btn

                  block
                  color="default"
                  variant="tonal"
                  @click="AddEmployeeToAttendance"
                >
                  Add
                </v-btn>
              </VCol>
            </VRow>
          </div>

          <div class="ma-7">

            <VRow>
              <VCol
                cols="5"
                sm="4"
              >
                <VTabs
                  v-model="currentTab"
                  direction="vertical"
                  class="v-tabs-pill"
                >
                  <VTab v-for="attendance in employeeAttendance">
                    <VIcon
                      start
                      icon="tabler-user"
                    />

                    {{attendance.result[0].employee.name}} {{attendance.result[0].employee.surname}}
                  </VTab>

                </VTabs>

              </VCol>

              <VCol
                cols="7"
                sm="8"
              >
                <VCard>
                  <VCardText>


                    <VWindow v-model="currentTab">
                      <VWindowItem  v-for="attendance in employeeAttendance">

                        <v-table >
                          <thead>
                          <tr class="pt-5">
                            <th class="text-left">
                              Quit Employee
                            </th>
                            <th class="text-left">

                              <VRow>
                                <VCol cols="4">
                                  <VSwitch
                                    v-model="attendance.result[0].employee.active"
                                    inset
                                  />
                                </VCol>


                                <VCol cols="8" v-if="attendance.result[0].employee.active">
                                  <AppDateTimePicker
                                    v-model="attendance.result[0].employee.quitDate"
                                    label="End Date"
                                  />
                                </VCol>

                              </VRow>



                            </th>
                          </tr>
                          <tr>
                            <th class="text-left">
                              Remove Employee
                            </th>
                            <th class="text-left">
                              <VCheckbox
                                v-model="removEmp"
                                :label="capitalizedLabel(toggleCheckboxThree)"
                                true-icon="tabler-check"
                                false-icon="tabler-circle-x"
                                color="error"
                                @click="removeEmployee(attendance)"

                              />
                            </th>
                          </tr>
                          <tr>
                            <th class="text-left">
                              date
                            </th>
                            <th class="text-left">
                              Presence
                            </th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <th class="text-left">
                              ALL DAYS
                            </th>
                            <th class="text-left">
                              <VCheckbox
                                v-model="attendance.allDays"
                                :label="capitalizedLabel(toggleCheckboxThree)"
                                true-icon="tabler-check"
                                false-icon="tabler-circle-x"
                                color="error"
                                @click="updateAll(attendance)"

                              />
                            </th>

                          </tr>

                          <tr v-for="_attendance in attendance.result">
                            <th class="text-left">
                              {{_attendance.date}}
                            </th>
                            <th class="text-left">
                              <VCheckbox
                                v-model="_attendance.present"
                                :label="capitalizedLabel(toggleCheckboxThree)"
                                true-icon="tabler-check"
                                false-icon="tabler-circle-x"
                                color="error"
                              />
                            </th>
                          </tr>
                          </tbody>
                        </v-table>
                      </VWindowItem>

                    </VWindow>
                  </VCardText>
                </VCard>
              </VCol>
            </VRow>
          </div>



          <VDivider />

          <VCardText class="d-flex align-center flex-wrap justify-space-between gap-4 py-3 px-5">
            <span class="text-sm text-disabled">
              {{ paginationData }}
            </span>

            <VPagination
              v-model="currentPage"
              size="small"
              :total-visible="5"
              :length="totalPage"
            />
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        md="3"
      >
        <VCard class="mb-8">
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
              @click="updateAttendance"
            >
              Update
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
</style>
<route lang="yaml">
meta:
  action: edit
  subject: attendances
</route>
