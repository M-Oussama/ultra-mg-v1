<script setup>
import AddNewUserDrawer from '@/views/apps/user/list/AddNewUserDrawer.vue'
import EditUserDrawer from '@/views/apps/user/list/EditUserDrawer.vue';
import ConfirmationDialog from '@/views/apps/user/list/ConfirmationDialog.vue';
import { useUserListStore } from '@/views/apps/user/useUserListStore'
import PERMISSIONS from '@/router/permissions.js'
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

const userListStore = useUserListStore()
const searchQuery = ref('')
const loading = ref(false)
const isTyping = ref(true)
const selectedRole = ref()
const selectedRoleId = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalUsers = ref(0)
let users = ref([])
let roles = ref([])
const loading2 = ref({
  isActive: false
})
// 👉 Fetching users
const fetchUsers = () => {
  loading2.value.isActive = true

  userListStore.fetchUsers({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     users.value = response.data.users.data
     totalPage.value = response.data.totalPage
     totalUsers.value = response.data.totalUsers
     roles.value = response.data.roles
    loading.value = false;
    loading2.value.isActive = false

    // Focus on the text field after loading is complete
    // Focus on the text field after loading is complete


  }).catch(error => {
    loading2.value.isActive = false


  })
}



watchEffect(fetchUsers)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchUsers
watch(searchQuery, () => {
  loading.value = true;
  fetchUsers();
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


const isAddNewUserDrawerVisible = ref(false)
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

const addNewUser = userData => {
  loading2.value.isActive = true
  userListStore.addUser(userData).then(response => {
    // refetch User
    fetchUsers()

    successMiddleware(response.data.message)
  }).catch(error => {
    loading2.value.isActive = false
    errorsMiddleware(error.response.data.message)
  })

}
const updateUser = userData => {
  loading2.value.isActive = true
  userListStore.updateUser(userData).then(response => {
    // refetch User
    fetchUsers()

    successMiddleware(response.data.message)
  }).catch(error => {
    loading2.value.isActive = false

    errorsMiddleware(error.response.data.message)
  })


}
const deleteUser = userData =>  {
  loading2.value.isActive = true
  userListStore.deleteUser(userData).then(response => {
    // refetch User
    fetchUsers()

    successMiddleware(response.data.message)
  }).catch(error => {
    loading2.value.isActive = false

    errorsMiddleware(error.response.data.message)
  })


}

const openUpdateDrawer = (user) => {
  isEditUserDrawerVisible.value = true;
  selectedUser = user;
  selectedRoleId.value = user.role_id;

}

const openConfirmationDialog = (user) => {
  isDialogVisible.value = true;
  selectedUser = user;
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
        <VCard title="Users">
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
                v-if="$can(PERMISSIONS.USER.ADD, PERMISSIONS.USER.SUBJECT)"
                prepend-icon="tabler-plus"
                @click="isAddNewUserDrawerVisible = true"
              >
                Add New User
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
                  USER
                </th>
                <th scope="col">
                  email
                </th>


                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="user in users"
                :key="user.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{user.id}}
                </td>

                <!-- 👉 User -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      :color="resolveUserRoleVariant(user.role).color"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="user.avatar"
                        :src="user.avatar"
                      />
                      <span v-else>{{ user.name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: user.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ user.name }}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">@{{ user.email }}</span>
                    </div>
                  </div>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ user.email }}</span>
                </td>


                <!-- 👉 Actions -->
                <td
                  class="text-center"
                  style="width: 5rem;"
                >
                  <VBtn
                    v-if="$can(PERMISSIONS.USER.EDIT, PERMISSIONS.USER.SUBJECT)"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openUpdateDrawer(user)"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-edit"

                    />
                  </VBtn>

                  <VBtn
                    v-if="$can(PERMISSIONS.USER.DELETE, PERMISSIONS.USER.SUBJECT)"
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openConfirmationDialog(user)"

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
            <tfoot v-show="!users.length">
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
    <AddNewUserDrawer
      v-model:isDrawerOpen="isAddNewUserDrawerVisible"
      v-model:roles="roles"
      @user-data="addNewUser"
    />

    <!-- 👉 Edit User -->
    <EditUserDrawer
      v-model:isDrawerOpen="isEditUserDrawerVisible"
      v-model:roles="roles"
      v-model:role="selectedRoleId"
      v-model:user = "selectedUser"
      @user-data="updateUser"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:user="selectedUser"
      @user-data="deleteUser"
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
  subject: users
</route>
