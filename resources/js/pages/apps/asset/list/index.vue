<script setup>
import AddNewDrawer from '@/views/apps/asset/list/AddNewDrawer.vue'
import EditDrawer from '@/views/apps/asset/list/EditDrawer.vue';
import ConfirmationDialog from '@/views/apps/asset/list/ConfirmationDialog.vue';
import {useAssetStore} from '@/views/apps/asset/useAssetStore'
import PERMISSIONS from '@/router/permissions.js'
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

const assetStore = useAssetStore()
const searchQuery = ref('')
const loading = ref(false)
const isTyping = ref(true)
const selectedAsset = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalUsers = ref(0)
let users = ref([])
let assets = ref([])
let permissionLeft = ref([])
let permissionRight = ref([])

let updatePermissionLeft = ref([])
let updatePermissionRight = ref([])
const loading2 = ref({
  isActive: false
})
// 👉 Fetching users
const fetchData = () => {
  loading2.value.isActive = true

  assetStore.fetchData({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     assets.value = response.data.assets
    loading.value = false;
    loading2.value.isActive = false

    // Focus on the text field after loading is complete

  }).catch(error => {
    loading2.value.isActive = false


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

const resolveUserAssetVariant = asset => {
  if (asset === 'subscriber')
    return {
      color: 'warning',
      icon: 'tabler-user',
    }
  if (asset === 'author')
    return {
      color: 'success',
      icon: 'tabler-circle-check',
    }
  if (asset === 'maintainer')
    return {
      color: 'primary',
      icon: 'tabler-chart-pie-2',
    }
  if (asset === 'editor')
    return {
      color: 'info',
      icon: 'tabler-pencil',
    }
  if (asset === 'admin')
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
  loading2.value.isActive = true
  assetStore.addAsset(objectData).then(response =>{
    // refetch User
    fetchData()
    successMiddleware(response.data.message)
  }).catch(e =>{
    errorsMiddleware(e.response.data.message)

    loading2.value.isActive = false;

  })

}
const updateAsset = objectData => {
  loading2.value.isActive = true
  assetStore.updateAsset(objectData).then(response =>{
    // refetch User
    fetchData()
    successMiddleware(response.data.message)
  }).catch(e =>{
    errorsMiddleware(e.response.data.message)

    loading2.value.isActive = false;

  })
}
const deleteAsset = objectData =>  {
  loading2.value.isActive = true
  assetStore.deleteAsset(objectData).then(response =>{
    // refetch User
    fetchData()
    successMiddleware(response.data.message)
  }).catch(e =>{
    errorsMiddleware(e.response.data.message)

    loading2.value.isActive = false;

  })
}

const openUpdateDrawer = (asset) => {
  isEditUserDrawerVisible.value = true;
  selectedAsset.value = asset;

}

const openConfirmationDialog = (asset) => {
  isDialogVisible.value = true;
  selectedAsset.value = asset;
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
        <VCard title="Assets">
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
                Add Asset
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
                  Asset
                </th>
                <th scope="col">
                  LAST MAINTENANCE
                </th>
                <th scope="col">
                  NEXT MAINTENANCE
                </th>



                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="asset in assets"
                :key="asset.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{asset.id}}
                </td>

                <!-- 👉 User -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      :color="resolveUserAssetVariant(asset.asset).color"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="asset.avatar"
                        :src="asset.avatar"
                      />
                      <span v-else>{{ asset.name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: asset.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ asset.name }}
                        </RouterLink>
                      </h6>

                    </div>
                  </div>
                </td>

                <td>
                  {{asset.last_maintenance_date}}
                </td>
                <td>
                  {{asset.next_maintenance_date}}
                </td>



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
                    @click="openUpdateDrawer(asset)"

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
                    @click="openConfirmationDialog(asset)"

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
            <tfoot v-show="!assets.length">
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
      v-model:isDrawerOpen="isEditUserDrawerVisible"
      v-model:asset = "selectedAsset"
      @object-data="updateAsset"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:asset="selectedAsset"
      @object-data="deleteAsset"
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
  subject: assets
</route>
