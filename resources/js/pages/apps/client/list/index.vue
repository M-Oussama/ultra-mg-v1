<script setup>
import AddNewClientDrawer from '@/views/apps/client/list/AddNewClientDrawer.vue'
import EditClientDrawer from '@/views/apps/client/list/EditClientDrawer.vue';
import ConfirmationDialog from '@/views/apps/client/list/ConfirmationDialog.vue';
import {useClientListStore} from "@/views/apps/client/useClientListStore";
import ClientMonthlyReport from '@/views/dashboards/crm/ClientMonthlyReport.vue'
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

const clientListStore = useClientListStore()
const searchQuery = ref('')
const loading = ref(false)
const isTyping = ref(true)
const isShoppingDialog = ref(false)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalClients = ref(0)
const cities = ref([])
let clients = ref([])
let clientsAll = ref([])
let currentTab = ref('Appetizers')
let loading2 = ref({
  isActive: false,
})

const items = [
  {
    id: 1,
    name: 'Home'
  },
  {
    id: 2,
    name: 'Record'
  },
  {
    id: 3,
    name: 'Payments'
  },
  {
    id: 4,
    name: 'State'
  },



]
const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice dragée macaroon.'


// 👉 Fetching Clients
const fetchClients = () => {
  loading2.value.isActive = true;
  clientListStore.fetchClients({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
    loading2.value.isActive = false;
     clients.value = response.data.clients.data
    console.log(response.data.clientsAll);
    clientsAll.value = response.data.clientsAll
    console.log(clients.value)
     totalPage.value = response.data.totalPage
     cities.value = response.data.cities
     totalClients.value = response.data.totalClients
    loading.value = false;
    // Focus on the text field after loading is complete
    // Focus on the text field after loading is complete


  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;
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
  clientListStore.addClient(clientData).then(response => {
    // refetch User
    fetchClients()
  }).catch(error => {
    console.log(error)
  })


}

const updateClient = clientData => {
  clientListStore.updateClient(clientData).then(response => {
    // refetch User
    fetchClients()
  }).catch(error => {
    console.log(error)
  })


}

const deleteClient = clientData =>  {
  clientListStore.deleteClient(clientData).then(response => {
    // refetch User
    fetchClients()
  }).catch(error => {
    console.log(error)
  })


}

const openUpdateDrawer = (client) => {
  isEditClientDrawerVisible.value = true;
  selectedClient = client;
}

const openConfirmationDialog = (client) => {
  isDialogVisible.value = true;
  selectedClient = client;

}
const openDialogData = (client) => {
  isShoppingDialog.value = true;
  selectedClient = client;

}
const exportList = () => {
  var array = [];
  var counter = 1;
  array.push(['ID', 'Date', 'Sale'])
  console.log(clientsAll)
  for (let i = 0; i <clientsAll._rawValue.length ; i++) {

   array.push([counter,clientsAll._rawValue[i].name,  clientsAll._rawValue[i].surname])

    counter++;
  }
  console.log(array);


  const doc = new jsPDF({
      orientation: "portrait",

      format: "letter"
    }
  );
  let date = new Date();
  let day = String(date.getDate()).padStart(2, '0');
  let month = String(date.getMonth() + 1).padStart(2, '0'); // Adding 1 because getMonth() returns zero-based month
  let year = date.getFullYear();
  let current_date = day + '/' + month + '/' + year;
  // doc.setFontSize(16).text("CUSTOMER LOG", 100,15, { align: "center", maxWidth: "100"});
  // doc.setFontSize(8).text("Client: "+client.value.name+ ' '+ client.value.surname, 15,25, { align: "left", maxWidth: "100"});
  // doc.setFontSize(8).text("Address: "+client.value.address, 100,25, { align: "center", maxWidth: "100"});
  // doc.setFontSize(8).text("Email: "+(client.value.email === null ? '' : client.value.email), 195,25, { align: "right", maxWidth: "100"});
  // doc.setFontSize(8).text("Phone: "+(client.value.phone === null ? '' : client.value.phone), 15,35, { align: "left", maxWidth: "100"});
  // doc.setFontSize(8).text("From:        To:      ", 97,35, { align: "center", maxWidth: "100"});
  // doc.setFontSize(8).text("Date: "+current_date, 187,35, { align: "right", maxWidth: "100"});
  autoTable(doc, {
    columnStyles: { 0: { halign: 'center' } }, // Cells in first column centered and green
    margin: { top: 40 , left: 10},
    body:

    array
    ,
  })

  doc.save('table.pdf');
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
                @click="exportList"
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
                  City
                </th>
                <th scope="col">
                  Phone
                </th>
                <th scope="col">
                  Balance
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
                  <span class="text-capitalize text-base font-weight-semibold">{{ client.city.name }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ client.phone }}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ parseFloat(client.balance.balance).toFixed(2) }} DZD</span>
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
                    :to="{ name: 'apps-client-log-id', params: { id: client.id } }"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-currency-dollar"

                    />
                  </VBtn>
                  <VBtn
                    icon
                    size="x-small"
                    color="default"
                    variant="text"
                    @click="openDialogData(client)"

                  >
                    <VIcon
                      size="22"
                      icon="tabler-shopping-cart"

                    />
                  </VBtn>


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

    <VDialog
      v-model="isShoppingDialog"
      scrollable
      class="v-dialog-sm"
      max-width="700"
      width="auto"

      content-class="scrollable-dialog"
    >
<!--      &lt;!&ndash; Dialog Activator &ndash;&gt;-->
<!--      <template #activator="{ props }">-->
<!--        <VBtn v-bind="props">-->
<!--          Open Dialog-->
<!--        </VBtn>-->
<!--      </template>-->

      <!-- Dialog close btn -->
      <DialogCloseBtn @click="isShoppingDialog = !isShoppingDialog" />

      <!-- Dialog Content -->
      <VCard title=" ">


        <VCardText class="d-flex justify-end gap-3 flex-wrap">
          <VTabs
            v-model="currentTab"
            grow
          >
            <VTab
              v-for="item in items"
              :key="item.id"
              :value="item.id"
            >
              {{ item.name }}
            </VTab>
          </VTabs>
          <VDivider />

          <VWindow
            v-model="currentTab"
            class="mt-6 pl-3 pa-1"
          >
            <VWindowItem
              :key="items[0].id"
              :value="items[0].id"
              width="150%"
            >
               <VRow>
                 <VCol
                   cols="6"
                   md="6"
                 >
                   <ClientMonthlyReport />
                 </VCol>

               </VRow>

            </VWindowItem>
            <VWindowItem

              :key="items[1].id"
              :value="items[1].id"
              class="mt-0"
              style="    margin-top: 0 !important;"
            >

              <VTable class="text-no-wrap mt-0 mb-10"   v-for="sale in selectedClient.sales" >

                <thead>
                <tr>
                  <th class="text-caption">
                    Product
                  </th>
                  <th class="text-caption">
                    Date
                  </th>
                  <th class="text-caption">
                    Quantity
                  </th>
                  <th class="text-caption">
                    Price
                  </th>
                  <th class="text-caption">
                    Total
                  </th>

                </tr>
                </thead>

                <tbody>
                <tr
                  v-for="item in sale.sale_items"
                  :key="item.id"
                >
                  <td class="text-caption">
                    {{item.product.name}}
                  </td>
                  <td class="text-caption">
                    {{item.sale_date}}
                  </td>
                  <td class="text-caption">
                    {{item.quantity}}
                  </td>
                  <td class="text-caption">
                    {{item.price}}
                  </td>
                  <td class="text-caption">
                    {{item.total_price}}
                  </td>
                </tr>
                <tr>
                  <td>

                  </td>
                  <td>

                  </td>
                  <td>

                  </td>
                  <td class="text-caption">
                    Total
                  </td>
                  <td class="text-caption">
                    {{sale.total_amount}}
                  </td>
                </tr>
                <tr>
                  <td>

                  </td>
                  <td>

                  </td>
                  <td>

                  </td>
                  <td class="text-caption">
                    Payment
                  </td>
                  <td class="text-caption">
                    {{sale.regulation}}
                  </td>
                </tr>
                <tr>
                  <td>

                  </td>
                  <td>

                  </td>
                  <td>

                  </td>
                  <td class="text-caption">
                    Rest
                  </td>
                  <td class="text-caption">
                    {{sale.balance}}
                  </td>
                </tr>
                </tbody>
              </VTable>
            </VWindowItem>
            <VWindowItem

              :key="items[2].id"
              :value="items[2].id"
              class="mt-0"
              style="width: -webkit-fill-available;"
              width="100%"
            >

                <VTable class="text-no-wrap mt-0 "   style="width: -webkit-fill-available;" >

                  <thead>
                  <tr>

                    <th >
                      Date
                    </th>
                    <th >
                      Amount
                    </th>
                  </tr>
                  </thead>

                  <tbody>
                  <tr
                    v-for="payment in selectedClient.payments"
                    :key="payment.id"
                  >
                    <td >
                      {{payment.payment_date}}
                    </td>
                    <td>
                      {{payment.amount_paid}}
                    </td>

                  </tr>

                  </tbody>
                </VTable>


            </VWindowItem>
            <VWindowItem

              :key="items[3].id"
              :value="items[3].id"
            >

            </VWindowItem>

          </VWindow>
          <VBtn @click="isShoppingDialog = false">
            Close
          </VBtn>
        </VCardText>
      </VCard>
    </VDialog>
    <!-- 👉 Add New Client -->
    <AddNewClientDrawer
      v-model:isDrawerOpen="isAddNewClientDrawerVisible"
      v-model:cities="cities"
      @client-data="addNewClient"
    />

    <!-- 👉 Edit Client -->
    <EditClientDrawer
      v-model:isDrawerOpen="isEditClientDrawerVisible"
      v-model:client = "selectedClient"
      v-model:cities="cities"
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
.v-theme--light{
  margin-top: 0 !important;

}
.v-window{
  width: -webkit-fill-available;
}
</style>
