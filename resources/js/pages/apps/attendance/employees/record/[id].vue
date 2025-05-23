<script setup>
import AddNewEmployeeDrawer from '@/views/apps/employee/list/AddNewEmployeeDrawer.vue'
import EditEmployeeDrawer from '@/views/apps/employee/list/EditEmployeeDrawer.vue';
import ConfirmationDialog from '@/views/apps/employee/list/ConfirmationDialog.vue';
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {useAttendanceStore} from "@/views/apps/attendance/useAttendanceStore";

const attendanceStore = useAttendanceStore()
const searchQuery = ref('')
const loading = ref(false)
const loading2 = ref({
  isActive:true
})
const route = useRoute()
const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalEmployees = ref(0)
let employees = ref([])

// 👉 Fetching Employees
const fetchEmployees = () => {
  loading2.value.isActive = true;
  attendanceStore.fetchEmployeesByAttendance(Number(route.params.id)).then(response => {
    employees.value = response.data.employees.data
     totalPage.value = response.data.totalPage
     totalEmployees.value = response.data.totalEmployees
     loading.value = false;
    loading2.value.isActive = false;
    console.log(response);
  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;
  })
}



watchEffect(fetchEmployees)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchEmployees
watch(searchQuery, () => {

  //loading.value = true;
  fetchEmployees();
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
  const firstIndex = employees.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = employees.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalEmployees.value } entries`
})

const addNewEmployee = employeeData => {
  loading2.value.isActive = true;
  employeeStore.addEmployee(employeeData).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
    // refetch User
    fetchEmployees()
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  });


}

const updateEmployee = employeeData => {
  loading2.value.isActive = true;
  employeeStore.updateEmployee(employeeData).then(response =>{
    successMiddleware(response.data.message)

    loading2.value.isActive = false;
    // refetch User
    fetchEmployees()
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })


}

const deleteEmployee = employeeData =>  {
  loading2.value.isActive = true;
  employeeStore.deleteEmployee(employeeData).then(response =>{
    loading2.value.isActive = false;
    successMiddleware(response.data.message)
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })

  // refetch Employee
  fetchEmployees()
}

const openUpdateDrawer = (employee) => {
  isEditEmployeeDrawerVisible.value = true;
  selectedEmployee = employee;
}

const openConfirmationDialog = (employee) => {
  isDialogVisible.value = true;
  selectedEmployee = employee;

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
        <VCard title="Employee Record">
          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <div
              class="me-3"
              style="width: 80px;"
            >
              <VSelect
                v-model="rowPerPage"
                density="compact"
                variant="outlined"
                :items="[10, 20, 30, 50]"
              />
            </div>

            <VSpacer />

            <div class="app-user-search-filter d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div style="width: 9rem;">
                <v-progress-circular
                  v-if="loading"
                  indeterminate
                  color="primary"
                ></v-progress-circular>
                <VTextField
                  v-else
                  @input="isTyping = true"
                  ref="searchField"
                  v-model="searchQuery"
                  placeholder="Search"
                  density="compact"
                />
              </div>

              <!-- 👉 Export button -->
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
              >
                Export
              </VBtn>

              <!-- 👉 Add Employee button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewEmployeeDrawerVisible = true"
              >
                New Employee
              </VBtn>
            </div>
          </VCardText>

          <VDivider />

          <VTable class="text-no-wrap">
            <!-- 👉 table head -->
            <thead>
              <tr>
                <th scope="col">
                  ID
                </th>
                <th scope="col">
                  Employee
                </th>

                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="employee in employees"
                :key="employee.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{employee.id}}
                </td>

                <!-- 👉 User -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"

                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="employee.avatar"
                        :src="employee.avatar"
                      />
                      <span v-else>{{ employee.name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: employee.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ employee.name }} {{employee.surname}}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">@{{ employee.email }}</span>
                    </div>


                  </div>
                </td>



                <!-- 👉 Actions -->
                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    :to="{ name: 'apps-attendance-employees-record-id', params: { id: employee.id, attendanceId: Number(route.params.id)  } }"


                  >
                    <VIcon
                      size="22"
                      icon="tabler-eye"

                    />
                  </VBtn>



                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-dots-vertical"
                    />


                  </VBtn>
                </td>
              </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!employees.length">
              <tr>
                <td
                  colspan="7"
                  class="text-center"
                >
                  No data available
                </td>
              </tr>
            </tfoot>
          </VTable>

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
<route lang="yaml">
meta:
  action: edit
  subject: attendances
</route>
