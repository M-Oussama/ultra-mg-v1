<script setup>
import AddNewClientDrawer from '@/views/apps/client/list/AddNewClientDrawer.vue'
import EditClientDrawer from '@/views/apps/client/list/EditClientDrawer.vue';
import ConfirmationDialog from '@/views/apps/client/list/ConfirmationDialog.vue';
import {useClientListStore} from "@/views/apps/client/useClientListStore";
import ClientMonthlyReport from '@/views/dashboards/crm/ClientMonthlyReport.vue'
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

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
const totalLogs = ref(0)
const cities = ref([])
let logs = ref([])
let client = ref()
let currentTab = ref('Appetizers')
let loading2 = ref({
  isActive: false,
})
const FromDate = ref(new Date(`01-01-${new Date().getFullYear()}`));
const ToDate = ref(new Date());
const heading = ref("Sample PDF Generator")
const  moreText = ref( [
  "This is another few sentences of text to look at it.",
  "Just testing the paragraphs to see how they format.",
  "jsPDF likes arrays for sentences.",
  "Do paragraphs wrap properly?",
  "Yes, they do!",
  "What does it look like?",
  "Not bad at all."
])
const  items = ref( [
  { title: "Item 1", body: "I am item 1 body text" },
  { title: "Item 2", body: "I am item 2 body text" },
  { title: "Item 3", body: "I am item 3 body text" },
  { title: "Item 4", body: "I am item 4 body text" }
])




let balance = ref(0)
const route = useRoute()
// const items = [
//   {
//     id: 1,
//     name: 'Home'
//   },
//   {
//     id: 2,
//     name: 'Record'
//   },
//   {
//     id: 3,
//     name: 'Payments'
//   },
//   {
//     id: 4,
//     name: 'State'
//   },
//
//
//
// ]
const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice dragée macaroon.'


// 👉 Fetching logs
const fetchClientLog = () => {
  loading2.value.isActive = true;
  clientListStore.fetchClientLog({
     client_id: Number(route.params.id),
     from_date: FromDate.value,
     to_date: ToDate.value,
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
    console.log(response)
    loading2.value.isActive = false;
     logs.value = response.data.logs.data
     client.value = response.data.client

     totalPage.value = response.data.totalPage
     cities.value = response.data.cities
     totalLogs.value = response.data.totalLogs
     loading.value = false;
    // Focus on the text field after loading is complete
    // Focus on the text field after loading is complete


  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;
  })
}



watchEffect(fetchClientLog)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchlogs
watch(searchQuery, () => {

  //loading.value = true;
  fetchClientLog();
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
  const firstIndex = logs.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = logs.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalLogs.value } entries`
})

const addNewClient = clientData => {
  clientListStore.addClient(clientData).then(response => {
    // refetch User
    fetchClientLog()
  }).catch(error => {
    console.log(error)
  })


}

const updateClient = clientData => {
  clientListStore.updateClient(clientData).then(response => {
    // refetch User
    fetchClientLog()
  }).catch(error => {
    console.log(error)
  })


}

const deleteClient = clientData =>  {
  clientListStore.deleteClient(clientData).then(response => {
    // refetch User
    fetchClientLog()
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

const getBalance = (log) => {
  if(log.type === "sale" ) {
    if(!log.counted) {
      log.total_balance = balance.value;
      if(parseFloat(log.amount) > 0) {
        log.total_balance += parseFloat(log.amount);
      }else {
        log.total_balance -= parseFloat(log.amount);
      }

      log.counted = true;
      balance.value = log.total_balance;

    }

  }
  else {
    if((!log.counted )) {
      log.total_balance = balance.value;
      if(parseFloat(log.amount) > 0) {
        log.total_balance -= parseFloat(log.amount);
      }else {
        log.total_balance += parseFloat(log.amount);
      }

      log.counted = true;
      balance.value = log.total_balance;

    }

  }


  return log.total_balance;

}

const exportLog =()=>{
  generateLogPDF(logs);
}
const exportAllLog = () => {

  let allLog = ref()
    loading2.value.isActive = true;
    clientListStore.exportAllLog({
      client_id: Number(route.params.id),
      from_date: FromDate.value,
      to_date: ToDate.value,

    }).then(response => {
      loading2.value.isActive = false;
      allLog.value = response.data.logs
      generateLogPDF(allLog)
    }).catch(error => {
      console.error(error)
      loading2.value.isActive = false;
    })

}

const generateLogPDF = (_logs) => {
  var array = [];
  var counter = 1;
  array.push(['ID', 'Date', 'Sale', 'Payment', 'Balance'])
  for (let i = 0; i <_logs._rawValue.length ; i++) {
    getBalance(_logs._rawValue[i])
    if(_logs._rawValue[i].type === "sale") {
      array.push([counter,_logs._rawValue[i].date,  _logs._rawValue[i].amount, _logs._rawValue[i].regulation > 0 ? _logs._rawValue[i].regulation : '' , _logs._rawValue[i].total_balance])
    } else {
      array.push([counter,_logs._rawValue[i].date,  '', _logs._rawValue[i].amount, _logs._rawValue[i].total_balance])

    }
    counter++;
  }
  console.log(array);


  array.push([counter,'','','',_logs._rawValue[_logs._rawValue.length-1].total_balance])
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
  doc.setFontSize(16).text("CUSTOMER LOG", 100,15, { align: "center", maxWidth: "100"});
  doc.setFontSize(8).text("Client: "+client.value.name+ ' '+ client.value.surname, 15,25, { align: "left", maxWidth: "100"});
  doc.setFontSize(8).text("Address: "+client.value.address, 100,25, { align: "center", maxWidth: "100"});
  doc.setFontSize(8).text("Email: "+(client.value.email === null ? '' : client.value.email), 195,25, { align: "right", maxWidth: "100"});
  doc.setFontSize(8).text("Phone: "+(client.value.phone === null ? '' : client.value.phone), 15,35, { align: "left", maxWidth: "100"});
  doc.setFontSize(8).text("From:        To:      ", 97,35, { align: "center", maxWidth: "100"});
  doc.setFontSize(8).text("Date: "+current_date, 187,35, { align: "right", maxWidth: "100"});
  autoTable(doc, {
    columnStyles: { 0: { halign: 'center' } }, // Cells in first column centered and green
    margin: { top: 40 , left: 10},
    body:

      array
    ,
  })

  doc.save(client.value.name+ '_'+ client.value.surname+'_log.pdf');
}
  // const columns = [
  //   { title: "Title", dataKey: "title" },
  //   { title: "Body", dataKey: "body" }
  // ];
  // const doc = new jsPDF({
  //   orientation: "portrait",
  //   unit: "in",
  //   format: "letter"
  // });
  // console.log(doc);
  // // text is placed using x, y coordinates
  // doc.setFontSize(16).text(heading.value, 0.5, 1.0);
  // // create a line under heading
  // doc.setLineWidth(0.01).line(0.5, 1.1, 8.0, 1.1);
  // // Using autoTable plugin
  // doc.autoTable({
  //   columns,
  //   body: items,
  //   margin: { left: 0.5, top: 1.25 }
  // });
  // // Using array of sentences
  // doc
  //   .setFont("helvetica")
  //   .setFontSize(12)
  //   .text(moreText, 0.5, 3.5, { align: "left", maxWidth: "7.5" });
  //
  // // Creating footer and saving file
  // doc
  //   .setFont("times")
  //   .setFontSize(11)
  //   .setFontStyle("italic")
  //   .setTextColor(0, 0, 255)
  //   .text(
  //     "This is a simple footer located .5 inches from page bottom",
  //     0.5,
  //     doc.internal.pageSize.height - 0.5
  //   )




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
        <VCard title="logs">
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
            <AppDateTimePicker
              v-model="FromDate"
              label="From"
            />
            <AppDateTimePicker
              v-model="ToDate"
              label="To"
            />
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
                @click="exportLog"
              >
                Export
              </VBtn>
              <VBtn
                variant="tonal"
                color="secondary"
                prepend-icon="tabler-screen-share"
                @click="exportAllLog"
              >
                ExportAll
              </VBtn>

              <!-- 👉 Add client button -->

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
                  Date
                </th>
                <th scope="col">
                  Achat
                </th>
                <th scope="col">
                  Regulation
                </th>
                <th scope="col">
                  Payment
                </th>
                <th scope="col">
                  Balance
                </th>

              </tr>
            </thead>
            <!-- 👉 table body -->

            <tbody>
              <tr
                v-for="(log, index) in logs"
                :key="log.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{parseInt(index)+1}}
                </td>

                <!-- 👉 User -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"

                      class="me-3"
                      size="38"
                    >


                      <span> {{ log.type.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: log.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ log.date }}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">{{ }}</span>
                    </div>
                  </div>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{log.type === 'sale' ? log.amount : ''}}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{log.type === 'sale' ? log.regulation : ''}}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{log.type === 'payment' ? log.amount : ''}}</span>
                </td>
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{getBalance(log)}}</span>
                </td>


                <!-- 👉 Actions -->

              </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!logs.length">
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
                    {{sale.amount}}
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
                      {{payment.amount}}
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
