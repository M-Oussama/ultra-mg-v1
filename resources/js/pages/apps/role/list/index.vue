<script setup>
import AddNewDrawer from '@/views/apps/role/list/AddNewDrawer.vue'
import EditDrawer from '@/views/apps/role/list/EditDrawer.vue';
import ConfirmationDialog from '@/views/apps/role/list/ConfirmationDialog.vue';
import { useRoleStore } from '@/views/apps/role/useRoleStore'
import PERMISSIONS from '@/router/permissions.js'
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

const roleStore = useRoleStore()
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
let roles = ref([])
let permissionLeft = ref([])
let permissionRight = ref([])

let updatePermissionLeft = ref([])
let updatePermissionRight = ref([])
let loading2 = ref({
  isActive: false,
})

// 👉 Fetching users
const fetchData = () => {
  loading2.value.isActive = true;
  roleStore.fetchData({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     roles.value = response.data.roles
    permissionLeft.value = response.data.permissions
     loading.value = false;
    loading2.value.isActive = false;
    // Focus on the text field after loading is complete

  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;

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

const resolveUserRoleVariant = role => {
  if (role === 'subscriber')
    return {
      color: 'warning',
      icon: 'tabler-user',
    }
  if (role === 'author')
    return {
      color: 'success',
      icon: 'tabler-circle-check',
    }
  if (role === 'maintainer')
    return {
      color: 'primary',
      icon: 'tabler-chart-pie-2',
    }
  if (role === 'editor')
    return {
      color: 'info',
      icon: 'tabler-pencil',
    }
  if (role === 'admin')
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
const isEditUserDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedUser = ref()

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
  roleStore.addRole(objectData).then(response =>{
    // refetch User


    successMiddleware(response.data.message)

    fetchData()
    loading2.value.isActive = false;

  }).catch(e=>{
    loading2.value.isActive = false;
    console.log(e)
    errorsMiddleware(e.response.data.message)
  })

}
const updateRole = objectData => {
  roleStore.updateRole(objectData).then(response =>{
    // refetch User
    successMiddleware(response.data.message)
    fetchData()
    loading2.value.isActive = false;

  }).catch(e=>{
    loading2.value.isActive = false;
    errorsMiddleware(e.response.data.message)
  })
}
const deleteRole = objectData =>  {
  roleStore.deleteRole(objectData).then(response =>{
    // refetch User
    successMiddleware(response.data.message)
    fetchData()
    loading2.value.isActive = false;

  }).catch(e=>{
    loading2.value.isActive = false;
    errorsMiddleware(e.response.data.message)
  })

}

const openUpdateDrawer = (role) => {
  isEditUserDrawerVisible.value = true;
  selectedRole.value = role;
  updatePermissionRight.value = role.permissions;
  updatePermissionLeft.value = role.remaining_permissions;

}

const openConfirmationDialog = (role) => {
  isDialogVisible.value = true;
  selectedRole.value = role;
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
        <VCard title="Roles">
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
                v-if="$can(PERMISSIONS.ROLE.ADD, PERMISSIONS.ROLE.SUBJECT)"

                prepend-icon="tabler-plus"
                @click="isDrawerVisible = true"
              >
                Add New Role
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
                  Role
                </th>



                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="role in roles"
                :key="role.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{role.id}}
                </td>

                <!-- 👉 User -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      :color="resolveUserRoleVariant(role.role).color"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="role.avatar"
                        :src="role.avatar"
                      />
                      <span v-else>{{ role.role.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: role.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ role.role }}
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
                    v-if="$can(PERMISSIONS.ROLE.EDIT, PERMISSIONS.ROLE.SUBJECT)"

                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openUpdateDrawer(role)"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-edit"

                    />
                  </VBtn>

                  <VBtn
                    v-if="$can(PERMISSIONS.ROLE.DELETE, PERMISSIONS.ROLE.SUBJECT)"

                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openConfirmationDialog(role)"

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
            <tfoot v-show="!roles.length">
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
      v-model:permission-left="permissionLeft"
      v-model:permission-right="permissionRight"
      @object-data="addNew"
    />

    <!-- 👉 Edit User -->
    <EditDrawer
      v-model:isDrawerOpen="isEditUserDrawerVisible"
      v-model:role = "selectedRole"
      v-model:permission-left="updatePermissionLeft"
      v-model:permission-right="updatePermissionRight"
      @object-data="updateRole"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:role="selectedRole"
      @object-data="deleteRole"
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
  subject: roles
</route>
