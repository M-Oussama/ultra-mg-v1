<script setup>
import AddNewBenefitDrawer from '@/views/apps/POS/benefit/AddNewBenefitDrawer.vue'
import EditBenefitDrawer from '@/views/apps/POS/benefit/EditBenefitDrawer.vue';
import ConfirmationDialog from '@/views/apps/POS/benefit/ConfirmationDialog.vue';
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
const totalBenefits = ref(0)
let benefits = ref([])
let months = ref([])
let years = ref([])
let clients = ref([])

// 👉 Fetching
const fetchBenefits = () => {
  loading2.value.isActive = true;
  saleStore.fetchBenefits({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
    benefits.value = response.data.benefits.data
    months.value = response.data.months

    years.value = response.data.years
    totalPage.value = response.data.totalPage
    totalBenefits.value = response.data.totalBenefits
    loading.value = false;
    loading2.value.isActive = false;
    // Focus on the text field after loading is complete
    // Focus on the text field after loading is complete


  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;

  })
}


watchEffect(fetchBenefits)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing
watch(searchQuery, () => {

  //loading.value = true;
  fetchBenefits();
});


const isAddNewBenefitDrawerVisible = ref({
  open: false
})
const isEditBenefitDrawerVisible = ref({
  open: false
})
const isDialogVisible = ref({
  open:false
})
const selectedBenefit = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = benefits.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = benefits.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalBenefits.value } entries`
})

const addBenefit= paymentData => {
 // saleStore.addPayment(paymentData)

  // refetch User
  fetchBenefits()
}

const updateBenefit = benefitData => {
  //saleStore.updatePayment(paymentData)

  // refetch User
  fetchBenefits()
}

const deleteBenefit = benefitData =>  {
  // refetch Payment
  fetchBenefits()

}

const openUpdateDrawer = (benefit) => {


  selectedBenefit.value = benefit;
  console.log(benefit)
   isEditBenefitDrawerVisible.value.open = true;


}

const openConfirmationDialog = (benefit) => {
  isDialogVisible.value.open = true;
  selectedBenefit.value = benefit;

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
        <VCard title="Benefits">
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
                @click="isAddNewBenefitDrawerVisible.open = true"
              >
               Add Benefit
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
                  AMOUNT
                </th>
                <th scope="col">
                  MONTH/YEAR
                </th>



                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="benefit in benefits"
                :key="benefit.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{benefit.id}}
                </td>

                <!-- 👉 User -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ parseFloat(benefit.benefit).toFixed(2) }} DZD</span>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ months[benefit.month-1].name }} / {{benefit.year}} </span>
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
                    :to="{ name: 'apps-POS-benefit-edit-id', params: { id: benefit.id } }"

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
                    @click="openConfirmationDialog(benefit)"

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


                  </VBtn>
                </td>
              </tr>
            </tbody>

            <!-- 👉 table footer  -->
            <tfoot v-show="!benefits.length">
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
    <AddNewBenefitDrawer
      v-model:isDrawerOpen="isAddNewBenefitDrawerVisible"
      v-model:months="months"
      v-model:years="years"
      v-model:benefit="benefits"
      v-model:loading="loading2"
      @benefit-data="addNewBenefit"
    />

    <!-- 👉 Edit Payment -->
    <EditBenefitDrawer
      v-model:isDrawerOpen="isEditBenefitDrawerVisible"
      v-model:benefit = "selectedBenefit"
      v-model:clients="clients"
      v-model:loading="loading2"
      @benefit-data="updateBenefit"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:benefit="selectedBenefit"
      v-model:loading="loading2"
      v-model:benefits="benefits"
      @benefit-data="deleteBenefit"
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