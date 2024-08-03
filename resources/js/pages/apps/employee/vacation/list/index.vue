<script setup>
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {useVacationStore} from "@/views/apps/employee/vacation/useVacationStore";
import EditEmployeeAttendanceDrawer from "@/views/apps/attendance/EditEmployeeAttendanceDrawer.vue";
import AddNewVacation from "@/views/apps/employee/vacation/AddNewVacation.vue";

const store = useVacationStore()
const searchQuery = ref('')
const title = ref('Employees')
const loading = ref(false)
const loading2 = ref({
  isActive:true
})
const route = useRoute()
const isTyping = ref(true)
const selectedRole = ref()
const email = ref('mahgounoussama23@gmail.com')
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalRecords = ref(0)
let list = ref([])
let isEditDrawerVisible = ref({
  open : false
})
let isDeleteDialogVisible = ref({
  open : false
})
let isAddDrawerOpen = ref({
  open : false
})
let disableEndDate = ref(false)
let disableStartDate = ref(true)

// 👉 Fetching List
const fetchList = () => {
  loading2.value.isActive = true;
  store.fetchListRecord(Number(route.params.id)).then(response => {
    list.value = response.data.employees.data
     totalPage.value = response.data.totalPage
     totalRecords.value = response.data.totalVacation
     loading.value = false;
    loading2.value.isActive = false;
    console.log(response);
  }).catch(error => {
    loading2.value.isActive = false;
  })
}



watchEffect(fetchList)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchList
watch(searchQuery, () => {

  //loading.value = true;
  fetchList();
});


const isDialogVisible = ref(false)
let selectedValue = ref(null)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = list.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = list.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalRecords.value } entries`
})

const addNewEmployee = data => {
  loading2.value.isActive = true;
  employeeStore.addEmployee(data).then(response =>{
    successMiddleware(response.data.message)
    loading2.value.isActive = false;
    // refetch User
    fetchList()
  }).catch(error => {
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  });


}

const updateEmployee = data => {
  loading2.value.isActive = true;
  employeeStore.updateEmployee(data).then(response =>{
    successMiddleware(response.data.message)

    loading2.value.isActive = false;
    // refetch User
    fetchList()
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })


}

const deleteEmployee = data =>  {
  loading2.value.isActive = true;
  employeeStore.deleteEmployee(data).then(response =>{
    loading2.value.isActive = false;
    successMiddleware(response.data.message)
  }).catch(error =>{
    errorsMiddleware(error.data.message)
    loading2.value.isActive = false;
  })

  // refetch Employee
  fetchList()
}

const openConfirmationDialog = (data) => {
  selectedValue.value = data;
  isEditDrawerVisible.value.open = true;
}

const openDeleteDialog = (data) => {
  selectedValue.value = data;
  isDeleteDialogVisible.value.open = true;
}

const openAddDrawer = () => {
  isAddDrawerOpen.value.open = true;
}
const onDelete = () => {
  //
  // loading2.value.isActive = true;
  // isDeleteDialogVisible.value.open = false;
  // attendanceStore.deleteCareer(selectedValue.value.id).then(response =>{
  //   loading2.value.isActive = false;
  //   successMiddleware(response.data.message)
  //   // refetch Employee
  //   fetchList()
  // }).catch(error =>{
  //   errorsMiddleware(error.data.message)
  //   loading2.value.isActive = false;
  // })


}
const closeConfirmationDialog= () => {
  isDeleteDialogVisible.value.open = false;

}

const periodInAlphabet = (employee)=> {
  let endDate = new Date(employee.end_date);

  if (!employee.end_date) {
    endDate = new Date(); // Use today's date if end date is null
  }

  const diffInMs = endDate - new Date(employee.start_date);
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));
  const years = Math.floor(diffInDays / 365);
  const months = Math.floor((diffInDays % 365) / 30);
  const days = diffInDays % 30;


  let period = '';

  if (years > 0) {
    period += ('0' + years).slice(-2)+' years' + ', ';
  }
  if (months > 0) {
    period += ('0' + months).slice(-2)+ ' months' + ', ';
  }
  if (days > 0) {
    period += ('0' + days).slice(-2)+' days'
  }

  // Remove trailing comma and space
  period = period.replace(/,\s*$/, '');

  return period;
}
const convertToAlphabet = (number, unit) =>{
  const alphabet = ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten',
    'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen',
    'nineteen', 'twenty', 'twenty-one', 'twenty-two', 'twenty-three', 'twenty-four', 'twenty-five',
    'twenty-six', 'twenty-seven', 'twenty-eight', 'twenty-nine', 'thirty', 'thirty-one'];

  if (number <= 31) {
    return alphabet[number] + ' ' + unit + (number !== 1 ? 's' : '');
  } else {
    return number + ' ' + unit + (number !== 1 ? 's' : '');
  }
}

const sendFile = (employee) => {
  // console.log(employee);
  // loading2.value.isActive = true;
  // attendanceStore.generateEmail(employee.id, email.value).then(response =>{
  //   loading2.value.isActive = false;
  //   successMiddleware(response.data.message)
  // }).catch(error =>{
  //   errorsMiddleware(error.data.message)
  //   loading2.value.isActive = false;
  // })

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
        <VCard :title="title">
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
              <VBtn

                color="primary"
                prepend-icon="tabler-plus"
                @click="openAddDrawer()"
              >
                New Vacation
              </VBtn>
              <!-- 👉 Add record button -->
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
                  Start Date
                </th>
                <th scope="col">
                  End Date
                </th>

                <th scope="col">
                  Period
                </th>

                <th scope="col">
                  Position
                </th>
                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="employee in list"
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


                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        {{ employee.start_date }}
                      </h6>

                    </div>


                  </div>
                </td>

                <td>
                  <div class="d-flex align-center">


                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        {{ employee.end_date }}
                      </h6>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-center">


                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        {{ periodInAlphabet(employee)}}
                      </h6>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-center">


                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        {{employee.position}}
                      </h6>
                    </div>
                  </div>
                </td>
                <!-- 👉 Actions -->
                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                  <VBtn
                    v-if="employee.last"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openConfirmationDialog(employee)"
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
                    :to="{ name: 'apps-employee-vacation-id', params: { id: employee.id } }"
                  >
                    <VIcon
                      size="22"
                      icon="tabler-user-check"

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
                      icon="tabler-trash"
                      @click="openDeleteDialog(employee)"
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
                      icon="tabler-send"
                      @click="sendFile(employee)"
                    />


                  </VBtn>

                  <VBtn
                    icon
                    variant="text"
                    color="default"
                    size="x-small"
                  >
                    <VIcon
                      :size="22"
                      icon="tabler-dots-vertical"
                    />
                    <VMenu activator="parent">
                      <VList density="compact">
                        <VListItem v-if="employee.BC" value="BC" :href="employee.BC">
                          <template #prepend>
                            <VIcon
                              size="22"
                              class="me-3"
                              icon="tabler-download"
                            />
                          </template>

                          <VListItemTitle>Birth Certificate</VListItemTitle>
                        </VListItem>

                        <VListItem v-if="employee.NC" value="NC" :href="employee.NC">
                          <template #prepend >
                            <VIcon
                              size="22"
                              class="me-3"
                              icon="tabler-download"
                            />
                          </template>

                          <VListItemTitle>National Card</VListItemTitle>
                        </VListItem>

                      </VList>
                    </VMenu>
                  </VBtn>
                </td>
              </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!list.length">
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
    <EditEmployeeAttendanceDrawer
      v-model:isDrawerOpen="isEditDrawerVisible"
      v-model:employee="selectedValue"
      v-model:disable-end-date="disableEndDate"
      v-model:loading = "loading2"
      v-model:disable-start-date = "disableStartDate"

    />

    <AddNewVacation
      v-model:isDrawerOpen="isAddDrawerOpen"
      v-model:employees="list"
      v-model:loading = "loading2"


    />


    <VDialog
      v-model="isDeleteDialogVisible.open"
      persistent
      class="v-dialog-sm"

    >

      <!-- Dialog close btn -->
      <DialogCloseBtn @click="closeConfirmationDialog()" />

      <!-- Dialog Content -->
      <VCard title="Confirmation">
        <VCardText>
          Are you sure you want to delete this record ?
        </VCardText>

        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VBtn
            color="secondary"
            variant="tonal"
            @click="closeConfirmationDialog()"
          >
            Disagree
          </VBtn>
          <VBtn @click="onDelete">
            Agree
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
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
