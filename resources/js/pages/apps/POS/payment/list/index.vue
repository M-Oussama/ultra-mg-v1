<script setup>
import AddNewPaymentDrawer from '@/views/apps/POS/payment/AddNewPaymentDrawer.vue'
import EditPaymentDrawer from '@/views/apps/POS/payment/EditPaymentDrawer.vue';
import ConfirmationDialog from '@/views/apps/POS/payment/ConfirmationDialog.vue';
import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";

const saleStore = useSaleStore()
const searchQuery = ref('')
const loading = ref(false)
const loading2 = ref({
  isActive: true
})
const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalPayments = ref(0)
let payments = ref([])
let clients = ref([])
let invoicesNotPaid = ref([])

// 👉 Fetching payments
const fetchPayments = () => {
  loading2.value.isActive = true;
  saleStore.fetchPayments({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     payments.value = response.data.payments.data
  //   invoicesNotPaid.value = response.data.invoicesNotPaid

     clients.value = response.data.clients

     totalPage.value = response.data.totalPage
     totalPayments.value = response.data.totalPayments
    loading.value = false;
    loading2.value.isActive = false;
    // Focus on the text field after loading is complete
    // Focus on the text field after loading is complete


  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;

  })
}


watchEffect(fetchPayments)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchpayments
watch(searchQuery, () => {

  //loading.value = true;
  fetchPayments();
});


const isAddNewPaymentDrawerVisible = ref({
  open: false
})
const isEditPaymentDrawerVisible = ref({
  open: false
})
const isDialogVisible = ref({
  open:false
})
const selectedPayment = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = payments.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = payments.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalPayments.value } entries`
})

const addNewPayment = paymentData => {
 // saleStore.addPayment(paymentData)

  // refetch User
  fetchPayments()
}

const updatePayment = paymentData => {
  //saleStore.updatePayment(paymentData)

  // refetch User
  fetchPayments()
}

const deletePayment =paymentData =>  {
  // refetch Payment
  fetchPayments()
  console.log("fetch")
}

const openUpdateDrawer = (payment) => {

  selectedPayment.value = payment;
   isEditPaymentDrawerVisible.value.open = true;


}

const openConfirmationDialog = (payment) => {
  isDialogVisible.value.open = true;
  selectedPayment.value = payment;

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
        <VCard title="Payments">
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

              <!-- 👉 Add payment button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewPaymentDrawerVisible.open = true"
              >
               New Payment
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
                  Amount
                </th>
                <th scope="col">
                  Date
                </th>


                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="payment in payments"
                :key="payment.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{payment.id}}
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
                        v-if="payment.avatar"
                        :src="payment.avatar"
                      />
                      <span v-else>{{ payment.client.name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: payment.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ payment.client.name }} {{ payment.client.surname }}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">@{{ payment.client.email }}</span>
                    </div>
                  </div>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ parseFloat(payment.amount_paid).toFixed(2) }} DZD</span>
                </td>
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ payment.payment_date }}</span>
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
                    @click="openUpdateDrawer(payment)"

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
                    @click="openConfirmationDialog(payment)"

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
            <tfoot v-show="!payments.length">
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

    <!-- 👉 Add New Payment -->
    <AddNewPaymentDrawer
      v-model:isDrawerOpen="isAddNewPaymentDrawerVisible"
      v-model:clients="clients"
      v-model:loading="loading2"
      v-model:invoicesNotPaid="invoicesNotPaid"
      @payment-data="addNewPayment"
    />

    <!-- 👉 Edit Payment -->
    <EditPaymentDrawer
      v-model:isDrawerOpen="isEditPaymentDrawerVisible"
      v-model:payment = "selectedPayment"
      v-model:clients="clients"
      v-model:loading="loading2"
      @payment-data="updatePayment"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:payment="selectedPayment"
      v-model:loading="loading2"
      v-model:payments="payments"
      @payment-data="deletePayment"
      v-if="isDialogVisible.open"
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
