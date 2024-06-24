<script setup>
import { avatarText } from '@core/utils/formatters'
import {useReturnStore} from "@/views/apps/POS/return/usereturnStore";
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import ConfirmationDialog from "@/views/apps/POS/return/list/ConfirmationDialog.vue";

const returnStore = useReturnStore()
const searchQuery = ref('')
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalReturns = ref(0)
const returns = ref([])
const selectedRows = ref([])
const isAddPaymentSidebarActive = ref(false)
const clients = ref([])
const from = ref('')
const to = ref('')
const isDialogVisible = ref(false)
let selectedReturn = ref()
const selectedClient = ref('')
const statusItems = ref([
  {
    id:1,
    name:"PAID"
  },
  {
    id:0,
    name:"ON HOLD"
  }
])
const fetchReturn = () =>{
  returnStore.fetchReturns({
    searchValue: searchQuery.value,
    status: selectedStatus.value,
    perPage: rowPerPage.value,
    currentPage: currentPage.value,
    client_id: selectedClient.value,
    from: from.value,
    to: to.value
  }).then(response => {
    loading.value.isActive = false;
    returns.value = response.data.returns
    clients.value = response.data.clients
    totalPage.value = response.data.totalPage
    totalReturns.value = response.data.totalReturns
  }).catch(error => {
    loading.value.isActive = false;
    console.log(error)
  })
}
// 👉 Fetch Invoices
watchEffect(() => {
  fetchReturn()
})

// 👉 Fetch Invoices
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = returns.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = returns.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalReturns.value } entries`
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

const resolveInvoiceStatusVariantAndIcon = _product_return => {
  if(_product_return !== undefined){
    if(_product_return.paid) {
      return {
        variant: 'success',
        icon: 'tabler-circle-check',
      }
    }
  }
    return {
      variant: 'error',
      icon: 'tabler-x',
    }

}
const _product_return = ref();
const openPaymentDrawer = (item) => {
  isAddPaymentSidebarActive.value = true;
  _product_return.value =item;
}
const loading = ref({
  isActive :true
})

const openConfirmationDialog = (_product_return) => {
  isDialogVisible.value = true;
  selectedReturn = _product_return;
  console.log(selectedReturn)
}
const deleteReturn = data =>  {
  returnStore.deleteReturn(data).then(response => {
    // refetch User
    fetchReturn()
  }).catch(error => {
    console.log(error)
  })


}


const onDataChanged = () =>{
  loading.value.isActive = true;
  fetchReturn()

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
          v-if="returns"
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
              <div class="invoice-list-filter">
                <AppDateTimePicker
                  v-model="from"
                  label="From Date"
                  @update:modelValue="onDataChanged"
                />
              </div>
              <!-- 👉 Search  -->
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
              <div class="me-3">
                <!-- 👉 Create invoice -->
                <VBtn
                  prepend-icon="tabler-plus"
                  :to="{ name: 'apps-POS-return-add' }"
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
                ACTIONS
              </th>
            </tr>
            </thead>

            <!-- 👉 Table Body -->
            <tbody>
            <tr
              v-for="_product_return in returns"
              :key="_product_return.id"
              style="height: 3.75rem;"
            >
              <!-- 👉 Id -->
              <td>
                <RouterLink :to="{ name: 'apps-POS-return-preview-id', params: { id: _product_return.id } }">
                  #{{ _product_return.id }}
                </RouterLink>
              </td>

              <td class="text-center">
                <VTooltip>
                  <template #activator="{ props }">
                    <VAvatar
                      :size="30"
                      v-bind="props"
                      :color="resolveInvoiceStatusVariantAndIcon(_product_return).variant"
                      variant="tonal"
                    >
                      <VIcon
                        :size="20"
                        :icon="resolveInvoiceStatusVariantAndIcon(_product_return).icon"
                      />
                    </VAvatar>
                  </template>

                  <p class="mb-0">
                    {{ _product_return.invoiceStatus }}
                  </p>
                  <p class="mb-0" v-if="_product_return.paid">
                    Delivered
                  </p>
                  <p class="mb-0" v-else>
                    ON HOLD
                  </p>

                </VTooltip>
              </td>


              <!-- 👉 Client Avatar and Email -->
              <td>
                <div class="d-flex align-center">
                  <VAvatar
                    size="34"
                    :color="resolveInvoiceStatusVariantAndIcon(_product_return).variant"
                    variant="tonal"
                    class="me-3"
                  >
                    <VImg
                      v-if="_product_return.avatar"
                      :src="_product_return.avatar"
                    />
                    <span v-else>{{ avatarText(_product_return.client.name) }}</span>
                  </VAvatar>

                  <div class="d-flex flex-column">
                    <h6 class="text-base font-weight-medium mb-0">
                      {{ _product_return.client.name }}
                    </h6>
                    <span class="text-disabled text-sm">{{ _product_return.client.email }}</span>
                  </div>
                </div>
              </td>

              <!-- 👉 total -->
              <td class="text-center">
                {{ _product_return.total_amount }}  DZD
              </td>

              <!-- 👉 Date -->
              <td>{{ _product_return.date }}</td>



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
                  icon
                  variant="text"
                  color="default"
                  size="x-small"
                  @click="openConfirmationDialog(_product_return)"
                >
                  <VIcon
                    :size="22"
                    icon="tabler-trash"
                  />
                </VBtn>
                <VBtn
                  icon
                  variant="text"
                  color="default"
                  size="x-small"
                  :to="{ name: 'apps-POS-return-preview-id', params: { id: _product_return.id } }"
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

                      <VListItem :to="{ name: 'apps-POS-return-edit-id', params: { id: _product_return.id } }">
                        <template #prepend>
                          <VIcon
                            size="24"
                            class="me-3"
                            icon="tabler-pencil"
                          />
                        </template>

                        <VListItemTitle>Edit</VListItemTitle>
                      </VListItem>

                    </VList>
                  </VMenu>
                </VBtn>
              </td>
            </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!returns.length">
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
            v-model:product="selectedReturn"
            @preturn-data="deleteReturn"
            v-if="isDialogVisible"
          />
        </VCard>
      </VCol>

    <InvoiceAddPaymentDrawer :loading="loading" :data="_product_return" v-model:isDrawerOpen="isAddPaymentSidebarActive" v-if="isAddPaymentSidebarActive" />
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
</style>
