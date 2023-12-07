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
const isTyping = ref(true)
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
let attendances = ref({})
// 👉 Fetching Employees
const fetchAttendances = () => {
  loading2.value.isActive = true;
  attendanceStore.fetchAttendanceData(Number(route.params.id)).then(response => {
     attendance.value.employees = response.data.employees
     attendance.value.dates = response.data.dates
    console.log(attendance)
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

const addNewEmployee = employeeData => {
  loading2.value.isActive = true;
  attendanceStore.addEmployee(employeeData).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
    // refetch User
    fetchAttendances()
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
      <VCol cols="12">
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
                          <tr v-for="date in attendance.dates">
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
