<script setup>
import AddNewClientDrawer from '@/views/apps/client/list/AddNewClientDrawer.vue'
import EditClientDrawer from '@/views/apps/client/list/EditClientDrawer.vue';
import ConfirmationDialog from '@/views/apps/client/list/ConfirmationDialog.vue';
import {useClientListStore} from "@/views/apps/client/useClientListStore";

const clientListStore = useClientListStore()
const searchQuery = ref('')
const loading = ref(false)
const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalClients = ref(0)
let clients = ref([])

// 👉 Fetching Clients
const fetchClients = () => {
  clientListStore.fetchClients({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {

     clients.value = response.data.clients.data
    console.log(clients.value)
     totalPage.value = response.data.totalPage
     totalClients.value = response.data.totalClients
    loading.value = false;
    // Focus on the text field after loading is complete
    // Focus on the text field after loading is complete


  }).catch(error => {
    console.error(error)

  })
}



watchEffect(fetchClients)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchClients
watch(searchQuery, () => {

  //loading.value = true;
  fetchClients();
});


const isAddNewClientDrawerVisible = ref(false)
const isEditClientDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedClient = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = clients.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = clients.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalClients.value } entries`
})

const addNewClient = clientData => {
  clientListStore.addClient(clientData)

  // refetch User
  fetchClients()
}

const updateClient = clientData => {
  clientListStore.updateClient(clientData)

  // refetch User
  fetchClients()
}

const deleteClient = clientData =>  {
  clientListStore.deleteClient(clientData)

  // refetch Client
  fetchClients()
}

const openUpdateDrawer = (client) => {
  isEditClientDrawerVisible.value = true;
  selectedClient = client;
}

const openConfirmationDialog = (client) => {
  isDialogVisible.value = true;
  selectedClient = client;

}

</script>

<template>
  <section>
    <VRow>
      <VCol cols="12">
        <VCard title="Clients">
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

              <!-- 👉 Add client button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewClientDrawerVisible = true"
              >
                Add New Client
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
                  Client
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
                v-for="client in clients"
                :key="client.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{client.id}}
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
                        v-if="client.avatar"
                        :src="client.avatar"
                      />
                      <span v-else>{{ client.name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: client.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ client.name }}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">@{{ client.email }}</span>
                    </div>
                  </div>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ client.email }}</span>
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
                    @click="openUpdateDrawer(client)"

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
                    @click="openConfirmationDialog(client)"

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
            <tfoot v-show="!clients.length">
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

    <!-- 👉 Add New Client -->
    <AddNewClientDrawer
      v-model:isDrawerOpen="isAddNewClientDrawerVisible"
      @client-data="addNewClient"
    />

    <!-- 👉 Edit Client -->
    <EditClientDrawer
      v-model:isDrawerOpen="isEditClientDrawerVisible"
      v-model:client = "selectedClient"
      @client-data="updateClient"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:client="selectedClient"
      @client-data="deleteClient"
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
