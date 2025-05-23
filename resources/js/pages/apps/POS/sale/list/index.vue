<script setup>
import { avatarText } from '@core/utils/formatters'
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import ConfirmationDialog from "@/views/apps/POS/sales/list/ConfirmationDialog.vue";
import PERMISSIONS from '@/router/permissions.js'

const saleStore = useSaleStore()
const searchQuery = ref('')
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalSales = ref(0)
const sales = ref([])
const clients = ref([])
const selectedClient = ref('')
const selectedRows = ref([])
const from = ref('')
const to = ref('')
const isAddPaymentSidebarActive = ref(false)

const isDialogVisible = ref(false)
let selectedSale = ref()
const statusItems = ref([
  {
    id:1,
    name:"Paid"
  },
  {
    id:2,
    name:"Not Paid"
  }
])

const fetchSale = () =>{

  saleStore.fetchSales({
    searchValue: searchQuery.value,
    status: selectedStatus.value,
    perPage: rowPerPage.value,
    currentPage: currentPage.value,
    client_id: selectedClient.value,
    from: from.value,
    to: to.value
  }).then(response => {
    loading.value.isActive = false;
    sales.value = response.data.sales
    clients.value = response.data.clients
    totalPage.value = response.data.totalPage
    totalSales.value = response.data.totalSales
  }).catch(error => {
    loading.value.isActive = false;
    console.log(error)
  })
}

// 👉 Fetch Invoices
watchEffect(() => {
  fetchSale()
})

// 👉 Fetch Invoices
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = sales.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = sales.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalSales.value } entries`
})

// 👉 Invoice balance variant resolver
const resolveInvoiceBalanceVariant = (balance, total) => {
  if (balance === total)
    return {
      status: 'Unpaid',
      chip: { color: 'error' },
    }
  if (balance === 0)
    return {
      status: 'Paid',
      chip: { color: 'success' },
    }
  
  return {
    status: balance,
    chip: { variant: 'text' },
  }
}

const resolveInvoiceStatusVariantAndIcon = sale => {
  if(sale.balance > 0) {

    if(sale.balance == sale.total_amount) {
      return {
        variant: 'error',
        icon: 'tabler-x',
      }
    } else {
      return {
        variant: 'info',
        icon: 'tabler-circle-half-2',
      }
    }

  } else {
    if(sale.balance < 0) {
      return {
        variant: 'warning',
        icon: 'tabler-alert-circle',
      }
    }
    if(sale.balance == 0) {
      return {
        variant: 'success',
        icon: 'tabler-circle-check',
      }
    }
  }
  return {
    variant: 'error',
    icon: 'tabler-chart-pie',
  }
}
const sale = ref();
const openPaymentDrawer = (item) => {
  isAddPaymentSidebarActive.value = true;
  sale.value =item;
}
const loading = ref({
  isActive :true
})

const openConfirmationDialog = (sale) => {
  isDialogVisible.value = true;
  selectedSale = sale;
  console.log(selectedSale)
}
const deleteSale = saleData =>  {
  saleStore.deleteSale(saleData).then(response => {
    // refetch User
    fetchSale()
  }).catch(error => {
    console.log(error)
  })


}

const onDataChanged = () =>{
  loading.value.isActive = true;
  fetchSale()

}
const togglePickup = sale =>{
  const isChecked = event.target.checked;
  sale.picked_up = isChecked; // update the frontend

 // Optional: Update backend
  loading.value.isActive = true;

  saleStore.updatePickup(sale, isChecked).then(response => {

    fetchSale()
  }).catch(error => {
    loading.value.isActive = false;
    console.log(error)
  })

}
</script>

<template>
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
      <VCol>
        <VCard
          v-if="sales"
          id="invoice-list"
        >
          <VCardText class="d-flex align-center flex-wrap gap-4">
            <!-- 👉 Rows per page -->




            <div
              class="d-flex align-center"
              style="width: 135px;"
            >
              <span class="text-no-wrap me-3">Show:</span>
              <VSelect
                v-model="rowPerPage"
                density="compact"
                :items="[10, 20, 30, 50, 100,500, 1000 ,5000]"
              />
            </div>

            <div
              class="d-flex align-center"
              style="width: 30%;"
            >
              <VAutocomplete
                clearable
                v-model="selectedClient"
                :items="clients"
                item-value="id"
                item-title="name"
                label="Client"
                placeholder="Client"
                @update:modelValue="onDataChanged"
              />

            </div>

            <div class="d-flex align-center flex-wrap gap-4">
              <!-- 👉 Search  -->
              <div class="invoice-list-filter">
                <AppDateTimePicker
                  v-model="from"
                  label="From Date"
                  @update:modelValue="onDataChanged"
                />
              </div>
              <div class="invoice-list-filter">
                <AppDateTimePicker
                  v-model="to"
                  label="To Date"
                  @update:modelValue="onDataChanged"
                />
              </div>



              <!-- 👉 Select status -->
              <div class="invoice-list-filter">
                <VSelect
                  v-model="selectedStatus"
                  label="Select Status"
                  clearable
                  item-value="id"
                  item-title="name"
                  clear-icon="tabler-x"
                  single-line
                  :items=statusItems
                  @update:modelValue="onDataChanged"
                />
              </div>
              <div class="me-3 ">
                <!-- 👉 Create invoice -->
                <VBtn
                  v-if="$can(PERMISSIONS.SALE.ADD, PERMISSIONS.SALE.SUBJECT)"

                  prepend-icon="tabler-plus"
                  :to="{ name: 'apps-POS-sale-add' }"
                >
                  Create
                </VBtn>
              </div>
            </div>





          </VCardText>

          <VDivider />

          <!-- SECTION Table -->
          <VTable class="text-no-wrap invoice-list-table">
            <!-- 👉 Table head -->
            <thead class="text-uppercase">
            <tr>
              <th scope="col">
                #ID
              </th>
              <th
                scope="col"
                class="text-center"
              >
                <VIcon icon="tabler-trending-up" />
              </th>

              <th scope="col">
                CLIENT
              </th>

              <th
                scope="col"
                class="text-center"
              >
                TOTAL
              </th>

              <th scope="col">
                Issued Date
              </th>

              <th scope="col">
                Driver
              </th>
               <th scope="col">
                Picked
              </th>

              <th scope="col">
                Picked up
              </th>



              <th scope="col">
                ACTIONS
              </th>
            </tr>
            </thead>

            <!-- 👉 Table Body -->
            <tbody>
            <tr
              v-for="sale in sales"
              :key="sale.id"
              style="height: 3.75rem;"
            >
              <!-- 👉 Id -->
              <td>
                <RouterLink :to="{ name: 'apps-POS-sale-preview-id', params: { id: sale.id } }">
                  #{{ sale.id }}
                </RouterLink>
              </td>


              <!-- 👉 Trending -->
              <td class="text-center">
                <VTooltip>
                  <template #activator="{ props }">
                    <VAvatar
                      :size="30"
                      v-bind="props"
                      :color="resolveInvoiceStatusVariantAndIcon(sale).variant"
                      variant="tonal"
                    >
                      <VIcon
                        :size="20"
                        :icon="resolveInvoiceStatusVariantAndIcon(sale).icon"
                      />
                    </VAvatar>
                  </template>

                  <p class="mb-0">
                    {{ sale.invoiceStatus }}
                  </p>
                  <p class="mb-0">
                    Balance: {{ sale.balance }}
                  </p>

                </VTooltip>
              </td>

              <!-- 👉 Client Avatar and Email -->
              <td>
                <div class="d-flex align-center">
                  <VAvatar
                    size="34"
                    :color="resolveInvoiceStatusVariantAndIcon(sale).variant"
                    variant="tonal"
                    class="me-3"
                  >
                    <VImg
                      v-if="sale.avatar"
                      :src="sale.avatar"
                    />
                    <span v-else>{{ avatarText(sale.client.name) }}</span>
                  </VAvatar>

                  <div class="d-flex flex-column">
                    <h6 class="text-base font-weight-medium mb-0">
                      {{ sale.client.name }}
                    </h6>
                    <span class="text-disabled text-sm">{{ sale.client.email }}</span>
                  </div>
                </div>
              </td>

              <!-- 👉 total -->
              <td class="text-center">
                {{ sale.total_amount }}  DZD
              </td>

              <!-- 👉 Date -->
              <td>{{ sale.sale_date }}</td>
              <td>{{ sale.driver ? sale.driver.full_name :'' }}</td>
              <td>
                <span v-if="sale.picked_up" class="badge badge-success">YES</span>
                <span v-else class="badge badge-danger">NO</span>


              </td>

              <td>

                <label class="switch">
                  <input type="checkbox"
                         :checked="!!sale.picked_up"
                         @change="togglePickup(sale, $event)">
                  <span class="slider round"></span>
                </label>
              </td>



              <!-- 👉 Actions -->
              <td style="width: 8rem;">
                <VBtn
                  icon
                  variant="text"
                  color="default"
                  size="x-small"
                >
                  <VIcon
                    icon="tabler-mail"
                    :size="22"
                  />
                </VBtn>

                <VBtn
                  v-if="$can(PERMISSIONS.SALE.DELETE, PERMISSIONS.SALE.SUBJECT)"

                  icon
                  variant="text"
                  color="default"
                  size="x-small"
                  @click="openConfirmationDialog(sale)"
                >
                  <VIcon
                    :size="22"
                    icon="tabler-trash"
                  />
                </VBtn>
                <VBtn
                  v-if="$can(PERMISSIONS.SALE.LIST, PERMISSIONS.SALE.SUBJECT)"

                  icon
                  variant="text"
                  color="default"
                  size="x-small"
                  :to="{ name: 'apps-POS-sale-preview-id', params: { id: sale.id } }"
                >
                  <VIcon
                    :size="22"
                    icon="tabler-eye"
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
                    <VList>
                      <VListItem value="download">
                        <template #prepend>
                          <VIcon
                            size="24"
                            class="me-3"
                            icon="tabler-download"
                          />
                        </template>

                        <VListItemTitle>Download</VListItemTitle>
                      </VListItem>

                      <VListItem :to="{ name: 'apps-POS-sale-edit-id', params: { id: sale.id } }"
                                 v-if="$can(PERMISSIONS.SALE.EDIT, PERMISSIONS.SALE.SUBJECT)"

                      >
                        <template #prepend>
                          <VIcon
                            size="24"
                            class="me-3"
                            icon="tabler-pencil"
                          />
                        </template>

                        <VListItemTitle>Edit</VListItemTitle>
                      </VListItem>
                      <VListItem value="payment" @click="openPaymentDrawer(sale)"
                                 v-if="$can(PERMISSIONS.PAYMENT.ADD, PERMISSIONS.PAYMENT.SUBJECT)"

                      >
                        <template #prepend>
                          <VIcon
                            size="24"
                            class="me-3"
                            icon="tabler-credit-card"

                          />
                        </template>

                        <VListItemTitle>Payment</VListItemTitle>
                      </VListItem>
                    </VList>
                  </VMenu>
                </VBtn>
              </td>
            </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!sales.length">
            <tr>
              <td
                colspan="8"
                class="text-center text-body-1"
              >
                No data available
              </td>
            </tr>
            </tfoot>
          </VTable>
          <!-- !SECTION -->

          <VDivider />

          <!-- SECTION Pagination -->
          <VCardText class="d-flex align-center flex-wrap gap-4 py-3">
            <!-- 👉 Pagination meta -->
            <span class="text-sm text-disabled">
        {{ paginationData }}
      </span>

            <VSpacer />

            <!-- 👉 Pagination -->
            <VPagination
              v-model="currentPage"
              size="small"
              :total-visible="5"
              :length="totalPage"
              @next="selectedRows = []"
              @prev="selectedRows = []"
            />

          </VCardText>
          <!-- !SECTION -->
          <ConfirmationDialog
            v-model:isDialogVisible="isDialogVisible"
            v-model:sale="selectedSale"
            @sale-data="deleteSale"
            v-if="isDialogVisible"
          />
        </VCard>
      </VCol>

    <InvoiceAddPaymentDrawer :loading="loading" :data="sale" v-model:isDrawerOpen="isAddPaymentSidebarActive" v-if="isAddPaymentSidebarActive" />
  </VRow>

</template>

<style lang="scss">
#invoice-list {
  .invoice-list-actions {
    inline-size: 8rem;
  }

  .invoice-list-filter {
    inline-size: 12rem;
  }
}


.badge {

  color: white;
  padding: 4px 8px;
  text-align: center;
  border-radius: 25px;
}
.badge-success{
  background-color: green;
}
.badge-danger{
  background-color: red;
}

.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0; left: 0;
  right: 0; bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px; width: 18px;
  left: 3px; bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #28a745;
}

input:checked + .slider:before {
  transform: translateX(26px);
}
</style>
<route lang="yaml">
meta:
  action: list
  subject: sales
</route>
