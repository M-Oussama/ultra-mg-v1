<script setup>
import AddNewDrawer from '@/views/apps/maintenance/list/AddNewDrawer.vue'
import EditDrawer from '@/views/apps/maintenance/list/EditDrawer.vue';
import ConfirmationDialog from '@/views/apps/maintenance/list/ConfirmationDialog.vue';
import {useMaintenanceStore} from '@/views/apps/maintenance/useMaintenanceStore'
import PERMISSIONS from '@/router/permissions.js'
import {successMiddleware} from "@/middlewares/successMiddleware";
import EditStatusDrawer from "@/views/apps/maintenance/list/EditStatusDrawer.vue";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

const maintenanceStore = useMaintenanceStore()
const searchQuery = ref('')
const loading = ref({
  isActive: false
})
const isTyping = ref(true)
const selectedMaintenance = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalMaintenances = ref(0)
let assets = ref([])
let users = ref([])
let maintenances = ref([])


// 👉 Fetching users
const fetchData = () => {

  loading.value.isActive = true
  maintenanceStore.fetchData({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     maintenances.value = response.data.maintenances
     assets.value = response.data.assets
     users.value = response.data.users
    loading.value.isActive = false
    // Focus on the text field after loading is complete

  }).catch(error => {
    console.error(error)
    loading.value.isActive = false

  })
}



watchEffect(fetchData)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchData
watch(searchQuery, () => {
  fetchData();
});

const status = [
  {
    title: 'Pending',
    value: 'pending',
  },
  {
    title: 'Active',
    value: 'active',
  },
  {
    title: 'Inactive',
    value: 'inactive',
  },
]

const resolveMaintenanceVariant = maintenance => {

  console.log(maintenance)
  if (maintenance.status === 'Started')
    return {
      color: 'info',
      icon: 'tabler-pencil',
    }
  if (maintenance.status === 'Active')
    return {
      color: 'primary',
      icon: 'tabler-chart-pie-2',
    }
  if (maintenance.status === 'Done')
    return {
      color: 'success',
      icon: 'tabler-check',
    }
  return {
    color: 'primary',
    icon: 'tabler-user',
  }
}


const isDrawerVisible = ref(false)
const isEditUserDrawerVisible = ref(false)
const isUpdateDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedUser = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = maintenances.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = maintenances.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalMaintenances.value } entries`
})

const addNew = objectData => {
  loading.value.isActive = true;
  maintenanceStore.addMaintenance(objectData).then(response =>{
    // refetch User
    loading.value.isActive = false;
    fetchData()
    successMiddleware(response.data.message)
  }).catch(e =>{
    errorsMiddleware(e.response.data.message)

    loading.value.isActive = false;

  })

}
const updateMaintenance = objectData => {
  maintenanceStore.updateComponent(objectData).then(response =>{
    // refetch User
    loading.value.isActive = false;
    fetchData()
    successMiddleware(response.data.message)
  }).catch(e =>{
    errorsMiddleware(e.response.data.message)

    loading.value.isActive = false;

  })

}
const deleteMaintenance = objectData =>  {
  maintenanceStore.deleteMaintenance(objectData).then(response =>{
    // refetch User
    loading.value.isActive = false;
    fetchData()
    successMiddleware(response.data.message)
  }).catch(e =>{
    errorsMiddleware(e.response.data.message)

    loading.value.isActive = false;

  })

}

const openUpdateDrawer = (maintenance) => {
  isEditUserDrawerVisible.value = true;
  selectedMaintenance.value = maintenance;

}
const openStatusUpdateDrawer = (maintenance) => {
  isUpdateDrawerVisible.value = true;
  selectedMaintenance.value = maintenance;

}

const openConfirmationDialog = (maintenance) => {
  isDialogVisible.value = true;
  selectedMaintenance.value = maintenance;
}
const daysLeft = (endDate) => {
  // Get the current date
  const currentDate = new Date();

  const nextDate = new Date(endDate);

  // Calculate the difference in time
  const differenceInTime = nextDate.getTime()  -   currentDate.getTime();

  // Calculate the difference in days
  const diffDays = Math.ceil(differenceInTime / (1000 * 3600 * 24));
  return diffDays >= 0 ? diffDays : 0;
}
// 👉 List
const userListMeta = [
  {
    icon: 'tabler-user',
    color: 'primary',
    title: 'Session',
    stats: '21,459',
    percentage: +29,
    subtitle: 'Total Users',
  },
  {
    icon: 'tabler-user-plus',
    color: 'error',
    title: 'Paid Users',
    stats: '4,567',
    percentage: +18,
    subtitle: 'Last week analytics',
  },
  {
    icon: 'tabler-user-check',
    color: 'success',
    title: 'Active Users',
    stats: '19,860',
    percentage: -14,
    subtitle: 'Last week analytics',
  },
  {
    icon: 'tabler-user-exclamation',
    color: 'warning',
    title: 'Pending Users',
    stats: '237',
    percentage: +42,
    subtitle: 'Last week analytics',
  },
]
</script>

<template>
  <section>
    <VRow>
      <v-overlay
        :model-value="loading.isActive"
        class="align-center justify-center"
      >
        <v-progress-circular
          color="primary"
          indeterminate
          size="64"
        ></v-progress-circular>
      </v-overlay>
      <VCol cols="12">
        <VCard title="Maintenances">
          <VDivider />

          <VCardText class="d-flex flex-wrap py-4 gap-4">
            <div
              class="me-3"

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

                <VTextField
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

              <!-- 👉 Add user button -->
              <VBtn
                v-if="$can(PERMISSIONS.MAINTENANCE.ADD, PERMISSIONS.MAINTENANCE.SUBJECT)"

                prepend-icon="tabler-plus"
                @click="isDrawerVisible = true"
              >
                Add Maintenance
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
                  Maintenance
                </th>
                <th scope="col">
                  LAST MAINTENANCE
                </th>
                <th scope="col">
                  NEXT MAINTENANCE
                </th>
                <th scope="col">
                  REST
                </th>


                <th scope="col">
                  CREATOR
                </th>
                <th scope="col">
                  ASSIGNED
                </th>



                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="maintenance in maintenances"
                :key="maintenance.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{maintenance.id}}
                </td>

                <!-- 👉 User -->
                <td>



                  <div class="d-flex align-center">


                    <VTooltip>
                      <template #activator="{ props }">
                        <VAvatar
                          :size="30"
                          v-bind="props"
                          :color="resolveMaintenanceVariant(maintenance).color"
                          variant="tonal"
                        >
                          <VIcon
                            :size="20"
                            :icon="resolveMaintenanceVariant(maintenance).icon"
                          />
                        </VAvatar>
                      </template>

                      <p class="mb-0">
                        Status : {{ maintenance.status }}
                      </p>

                    </VTooltip>

                    <div class="d-flex flex-column ml-5">
                      <h6 class="text-base font-weight-medium mb-0">
                       {{maintenance.name }}
                      </h6>
                      <span class="text-disabled text-sm"> @{{ maintenance.technician? maintenance.technician.name : '' }}</span>
                    </div>
                  </div>
                </td>

                <td>
                  {{maintenance.maintenance_date}}
                </td>
                <td>
                  {{maintenance.next_maintenance_date}}
                </td>
                <td>
                  {{daysLeft(maintenance.next_maintenance_date)}}
                </td>
                <td>
                  {{maintenance.technician? maintenance.technician.name : '' }}
                </td>
                <td>
                  {{maintenance.assigned? maintenance.assigned.name : '' }}
                </td>
                <!-- 👉 Actions -->
                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                  <VBtn
                    v-if="$can(PERMISSIONS.MAINTENANCE.EDIT, PERMISSIONS.MAINTENANCE.SUBJECT)"

                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openStatusUpdateDrawer(maintenance)"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-trending-up"

                    />
                  </VBtn>
                  <VBtn
                    v-if="$can(PERMISSIONS.MAINTENANCE.EDIT, PERMISSIONS.MAINTENANCE.SUBJECT)"

                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openUpdateDrawer(maintenance)"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-edit"

                    />
                  </VBtn>

                  <VBtn
                    v-if="$can(PERMISSIONS.MAINTENANCE.DELETE, PERMISSIONS.MAINTENANCE.SUBJECT)"

                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openConfirmationDialog(maintenance)"

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
            <tfoot v-show="!maintenances.length">
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

    <!-- 👉 Add New User -->
    <AddNewDrawer
      v-model:isDrawerOpen="isDrawerVisible"
      v-model:assets="assets"
      @object-data="addNew"
      v-model:users="users"
    />

    <!-- 👉 Edit User -->
    <EditDrawer
      v-model:isDrawerOpen="isEditUserDrawerVisible"
      v-model:maintenance = "selectedMaintenance"
      v-model:assets="assets"
      v-model:users="users"
      @object-data="updateMaintenance"
    />
    <!-- 👉 Edit User -->
    <EditStatusDrawer
      v-model:isDrawerOpen="isUpdateDrawerVisible"
      v-model:maintenance = "selectedMaintenance"
      v-model:assets="assets"
      @object-data="updateMaintenance"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:maintenance="selectedMaintenance"
      @object-data="deleteMaintenance"
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
  action: list
  subject: maintenance
</route>

