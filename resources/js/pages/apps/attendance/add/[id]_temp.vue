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
const minDate = ref("")
const maxDate = ref("")
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
let employees = ref({
  list:[]
})
let employee = ref({})
let removEmp = ref({})

// 👉 Fetching Employees
const fetchAttendances = () => {
  loading2.value.isActive = true;
  attendanceStore.fetchAttendanceData(Number(route.params.id)).then(response => {
     attendance.value.employees = response.data.employees
     employees.value.list = response.data.restEmployees
     attendance.value.dates = response.data.dates
      minDate.value = response.data.minDate
      maxDate.value = response.data.maxDate
      console.log(minDate)
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
  attendanceStore.submitAttendance(attendance, route.params.id).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
    router.push('/apps/attendance/preview/'+route.params.id)
  }).catch(error => {
    //errorsMiddleware(error)
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
const printName = employee =>{


  return employee.name === undefined ? 'Select An Employee' : employee.name + ' '+employee.surname;
}
const AddEmployeeToAttendance = () =>{
  var emp = getEmployee(employees.value.list,employee.value)
  attendance.value.employees.push({...emp})
  employees.value.list.splice(getEmployeeIndex(employees.value.list, employee),1)



}

const getEmployee = (employeeArray,employeeId) =>{
  for (let i = 0; i <employeeArray.length ; i++) {
    if(employeeArray[i].id === employeeId)
      return employeeArray[i];
  }
}

const updateAll = employee => {
  attendance.allDays = !attendance.allDays;

  for (let i = 0; i <attendance['result'].length ; i++) {
    attendance['result'][i].present = attendance.allDays;

  }
}
const removeEmployee = _employee => {

  employees.value.list.push({..._employee})

  attendance.value.employees.splice(getEmployeeIndex(attendance.value.employees, _employee.id),1)
}

watch(employees.value, () =>{
  console.log(employees)
})

const getEmployeeIndex = (employeeArray,employeeId) =>{
  for (let i = 0; i <employeeArray.length ; i++) {
    if(employeeArray[i].id === employeeId)
      return i;
  }
}

const changeQuitDate = (employee) =>{
  console.log(maxDate._value)
  for (let i = 0; i <employee.workingDays.length ; i++) {

      if(employee.workingDays[i].date >= employee.quitDate){
        employee.workingDays[i].present = false;
      }else{
        employee.workingDays[i].present = true;
      }
  }
}
const activeQuitDate = (employee) =>{
  console.log(employee.quitEmployee)
  if(employee.quitEmployee){
    employee.quitDate = maxDate._value;
    console.log(employee.quitDate)
    console.log(maxDate._value)
  }else{
    for (let i = 0; i <employee.workingDays.length ; i++) {
      employee.workingDays[i].present = true;
    }
  }

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
                  :items="employees.list"
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
                  <VTab v-for="employee in attendance.employees">
                    <VIcon
                      start
                      icon="tabler-user"
                    />
                    {{employee.name}} {{employee.surname}}
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
                      <VWindowItem  v-for="employee in attendance.employees">
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
                                    v-model="employee.quitEmployee"
                                    inset
                                    @change="activeQuitDate(employee)"
                                  />
                                </VCol>


                                <VCol cols="8" v-if="employee.quitEmployee">
                                  <AppDateTimePicker
                                    v-model="employee.quitDate"
                                    label="End Date"
                                    @change="changeQuitDate(employee)"
                                    :config="{  minDate: `${minDate}`, maxDate: `${maxDate}`  }"
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
                                @click="removeEmployee(employee)"

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
                                v-model="employee.allDays"
                                :label="capitalizedLabel(toggleCheckboxThree)"
                                true-icon="tabler-check"
                                false-icon="tabler-circle-x"
                                color="error"
                                @click="AllDaysPressed(employee)"
                              />
                            </th>
                          </tr>
                          <tr v-for="date in employee.workingDays">
                            <th class="text-left">
                              {{date.date}}
                            </th>
                            <th class="text-left">
                              <VCheckbox
                                v-model="date.present"
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
              @click="saveAttendance"
            >
              Save
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
  action: add
  subject: attendances
</route>
