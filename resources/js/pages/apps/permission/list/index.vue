<script setup>
import AddNewDrawer from '@/views/apps/permission/list/AddNewDrawer.vue'
import EditDrawer from '@/views/apps/permission/list/EditDrawer.vue';
import ConfirmationDialog from '@/views/apps/permission/list/ConfirmationDialog.vue';
import { usePermissionStore } from '@/views/apps/permission/usePermissionStore'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";

const permissionStore = usePermissionStore()
const searchQuery = ref('')
const loading = ref(false)
const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalUsers = ref(0)
let users = ref([])
let permissions = ref([])
const loading2 = ref({
  isActive: false
})
// 👉 Fetching users
const fetchData = () => {
  loading2.value.isActive = true
  permissionStore.fetchData({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     permissions.value = response.data.permissions
    loading2.value.isActive = false
    loading.value = false;
    // Focus on the text field after loading is complete

  }).catch(error => {
    loading2.value.isActive = false
    console.error(error)

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
  loading.value = true;
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

const resolveUserRoleVariant = permission => {
  if (permission === 'subscriber')
    return {
      color: 'warning',
      icon: 'tabler-user',
    }
  if (permission === 'author')
    return {
      color: 'success',
      icon: 'tabler-circle-check',
    }
  if (permission === 'maintainer')
    return {
      color: 'primary',
      icon: 'tabler-chart-pie-2',
    }
  if (permission === 'editor')
    return {
      color: 'info',
      icon: 'tabler-pencil',
    }
  if (permission === 'admin')
    return {
      color: 'secondary',
      icon: 'tabler-device-laptop',
    }
  
  return {
    color: 'primary',
    icon: 'tabler-user',
  }
}


const isDrawerVisible = ref(false)
const isEditDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedPermission = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = users.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = users.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalUsers.value } entries`
})

const addNew = objectData => {
  loading2.value.isActive = true
  permissionStore.addPermission(objectData).then(response => {
    // refetch User
    fetchData()

   successMiddleware(response.data.message)
  }).catch(error => {
    loading2.value.isActive = false
    errorsMiddleware(error.response.data.message)
  })

}
const updatePermission = objectData => {
  loading2.value.isActive = true
  permissionStore.updatePermission(objectData).then(response => {
    // refetch User
    fetchData()

    successMiddleware(response.data.message)
  }).catch(error => {
    loading2.value.isActive = false


    errorsMiddleware(error.response.data.message)
  })


}
const deletePermission = objectData =>  {
  loading2.value.isActive = true
  permissionStore.deletePermission(objectData).then(response => {
    // refetch User
    fetchData()

    successMiddleware(response.data.message)
  }).catch(error => {
    loading2.value.isActive = false


    errorsMiddleware(error.response.data.message)
  })


}

const openUpdateDrawer = (permission) => {
  isEditDrawerVisible.value = true;
  selectedPermission = permission;

}

const openConfirmationDialog = (permission) => {
  isDialogVisible.value = true;
  selectedPermission = permission;
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
        <VCard title="permissions">
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
              <div style="width: 10rem;">
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



              <!-- 👉 Add user button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isDrawerVisible = true"
              >
                Add New permission
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
                  permission
                </th>



                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="permission in permissions"
                :key="permission.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{permission.id}}
                </td>

                <!-- 👉 User -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      :color="resolveUserRoleVariant(permission.action).color"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="permission.avatar"
                        :src="permission.avatar"
                      />
                      <span v-else>{{ permission.action.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: permission.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ permission.action }}- {{permission.subject}}
                        </RouterLink>
                      </h6>

                    </div>
                  </div>
                </td>

                <!-- 👉 email -->



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
                    @click="openUpdateDrawer(permission)"

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
                    @click="openConfirmationDialog(permission)"

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

<!--                    <VMenu activator="parent">-->
<!--                      <VList>-->
<!--                        <VListItem-->
<!--                          title="View"-->
<!--                          :to="{ name: 'apps-user-view-id', params: { id: user.id } }"-->
<!--                        />-->
<!--                        <VListItem-->
<!--                          title="Suspend"-->
<!--                          href="javascript:void(0)"-->
<!--                        />-->
<!--                      </VList>-->
<!--                    </VMenu>-->
                  </VBtn>
                </td>
              </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!permissions.length">
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
      @object-data="addNew"
    />

    <!-- 👉 Edit User -->
    <EditDrawer
      v-model:isDrawerOpen="isEditDrawerVisible"
      v-model:permission = "selectedPermission"
      @object-data="updatePermission"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:permission="selectedPermission"
      @object-data="deletePermission"
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
  subject: permissions
</route>
