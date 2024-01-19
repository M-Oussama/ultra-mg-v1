<script setup>
import AddNewAttendanceDrawer from '@/views/apps/attendance/AddNewAttendanceDrawer.vue'
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
const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalAttendances = ref(0)
let attendances = ref([])
let months = [];
let years = [];
// 👉 Fetching Employees
const fetchAttendances = () => {
  loading2.value.isActive = true;
  attendanceStore.fetchAttendances({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     attendances.value = response.data.attendances.data
     months = response.data.months
     years = response.data.years
     totalPage.value = response.data.totalPage
     totalAttendances.value = response.data.totalAttendances
     loading.value = false;
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
const isAddNewAttendanceDrawerVisible = ref(false)
const isEditEmployeeDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedAttendance = ref()

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

const addNewAttendance = attendanceData => {
  loading2.value.isActive = true;
  attendanceStore.addAttendance(attendanceData).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
    // refetch User
    fetchAttendances()
  }).catch(error => {
    loading2.value.isActive = false;
    errorsMiddleware(error.response.data.message)
  });


}

const updateAttendance = attendanceData => {
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

const deleteAttendance = attendanceData =>  {
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
  selectedAttendance = employee;
}

const openConfirmationDialog = (employee) => {
  isDialogVisible.value = true;
  selectedAttendance = employee;

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
        <VCard title="Attendance">
          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <div
              class="me-3"
              style="width: 100px;"
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
              <div style="width: 6rem;">
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
                @click="isAddNewAttendanceDrawerVisible = true"
              >
                New Attendance
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
                  Month
                </th>
                <th scope="col">
                  Year
                </th>


                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="attendance in attendances"
                :key="attendance.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{attendance.id}}
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
                        v-if="attendance.avatar"
                        :src="attendance.avatar"
                      />
                      <span v-else>{{ months[attendance.month-1].name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: attendance.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{months[attendance.month-1].name}}
                        </RouterLink>
                      </h6>

                    </div>
                  </div>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ attendance.year }}</span>
                </td>


                <!-- 👉 Actions -->
                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                  <VBtn
                    v-if="!attendance.active"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    :to="{ name: 'apps-attendance-add-id', params: { id: attendance.id } }"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-edit"

                    />
                  </VBtn>
                  <VBtn
                    v-if="attendance.active"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    :to="{ name: 'apps-attendance-edit-id', params: { id: attendance.id } }"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-edit"

                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    :to="{ name: 'apps-attendance-preview-id', params: { id: attendance.id } }"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-viewfinder"

                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openConfirmationDialog(attendance)"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-trash"
                    />
                  </VBtn>

                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    :to="{ name: 'apps-attendance-employees-id', params: { id: attendance.id } }"

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
            <tfoot v-show="!attendances.length">
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
    <AddNewAttendanceDrawer
      v-model:isDrawerOpen="isAddNewAttendanceDrawerVisible"
      v-model:attendances="attendances"
      v-model:months="months"
      v-model:years="years"
      @attendance-data="addNewAttendance"
    />

    <!-- 👉 Edit Employee -->
<!--    <EditMonthDrawer-->
<!--      v-model:isDrawerOpen="isEditEmployeeDrawerVisible"-->
<!--      v-model:employee = "selectedEmployee"-->
<!--      @employee-data="updateEmployee"-->
<!--    />-->

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:attendance="selectedAttendance"
      @employee-data="deleteAttendance"
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
