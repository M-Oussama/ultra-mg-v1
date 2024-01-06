<script setup>
import AddNewProductDrawer from '@/views/apps/product/list/AddNewProductDrawer.vue'
import EditProductDrawer from '@/views/apps/product/list/EditProductDrawer.vue';
import ConfirmationDialog from '@/views/apps/product/list/ConfirmationDialog.vue';
import { useProductListStore } from '@/views/apps/product/useProductListStore'


const productListStore = useProductListStore()
const searchQuery = ref('')
const loading = ref(false)
const isTyping = ref(true)
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const rowPerPage = ref(10)
const currentPage = ref(1)
const totalPage = ref(1)
const totalProducts = ref(0)
let products = ref([])
let loading2 = ref({
  isActive: false,
})

// 👉 Fetching products
const fetchProducts = () => {
  loading2.value.isActive = true;
  productListStore.fetchProducts({
     searchValue: searchQuery.value,
     perPage: rowPerPage.value,
     currentPage: currentPage.value,
  }).then(response => {
     products.value = response.data.products.data
     totalPage.value = response.data.totalPage
     totalProducts.value = response.data.totalProducts
     loading.value = false;
    loading2.value.isActive = false;
  }).catch(error => {
    console.error(error)
    loading2.value.isActive = false;
  })
}
watchEffect(fetchProducts)

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})
// 👉 Watching changes in searchQuery and executing fetchProducts
watch(searchQuery, () => {
  loading.value = true;
  fetchProducts();
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

const resolveProductRoleVariant = role => {
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


const isAddNewProductDrawerVisible = ref(false)
const isEditProductDrawerVisible = ref(false)
const isDialogVisible = ref(false)
let selectedProduct = ref()

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = products.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = products.value.length + (currentPage.value - 1) * rowPerPage.value
  
  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalProducts.value } entries`
})

const addNewProduct = productData => {
  productListStore.addProduct(productData)

  // refetch product
  fetchProducts()
}
const updateProduct = productData => {

  productListStore.updateProduct(productData)

  // refrech products
  fetchProducts()
}
const deleteProduct = productData =>  {
  productListStore.deleteProduct(productData)

  // refetch product
  fetchProducts()
}

const openUpdateDrawer = (product) => {
  isEditProductDrawerVisible.value = true;
  selectedProduct = product;

}
const openConfirmationDialog = (product) => {
  isDialogVisible.value = true;
  selectedProduct = product;
}

// 👉 List
const productListMeta = [
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
        <VCard title="Products">
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
              <div style="width: 8rem;">
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

              <!-- 👉 Add product button -->
              <VBtn
                prepend-icon="tabler-plus"
                @click="isAddNewProductDrawerVisible = true"
              >
                Add New Product
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
                  Product
                </th>
                <th scope="col">
                  SKU
                </th>
                <th scope="col">
                  Stockable
                </th>


                <th scope="col">
                  ACTIONS
                </th>
              </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
              <tr
                v-for="product in products"
                :key="product.id"
                style="height: 3.75rem;"
              >
                <!-- 👉 ID -->
                <td>
                  {{product.id}}
                </td>

                <!-- 👉 Product -->
                <td>
                  <div class="d-flex align-center">
                    <VAvatar
                      variant="tonal"
                      :color="resolveProductRoleVariant(product.role).color"
                      class="me-3"
                      size="38"
                    >
                      <VImg
                        v-if="product.avatar"
                        :src="product.avatar"
                      />
                      <span v-else>{{ product.name.toUpperCase().charAt(0) }} </span>
                    </VAvatar>

                    <div class="d-flex flex-column">
                      <h6 class="text-base">
                        <RouterLink
                          :to="{ name: 'apps-user-view-id', params: { id: product.id } }"
                          class="font-weight-medium user-list-name"
                        >
                          {{ product.name }}
                        </RouterLink>
                      </h6>
                      <span class="text-sm text-disabled">@{{ product.brand }}</span>
                    </div>
                  </div>
                </td>

                <!-- 👉 email -->
                <td>
                  <span class="text-capitalize text-base font-weight-semibold">{{ product.SKU }}</span>
                </td>
                <td>
                  <VChip
                    :color="product.stockable ? 'success' : 'error'"
                    variant="elevated"
                  >
                    {{ product.stockable ? 'YES' : 'NO' }}
                  </VChip>
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
                    @click="openUpdateDrawer(product)"

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
                    @click="openConfirmationDialog(product)"

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
            <tfoot v-show="!products.length">
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

    <!-- 👉 Add New Product -->
    <AddNewProductDrawer
      v-model:isDrawerOpen="isAddNewProductDrawerVisible"
      @product-data="addNewProduct"
    />

    <!-- 👉 Edit Product -->
    <EditProductDrawer
      v-model:isDrawerOpen="isEditProductDrawerVisible"
      v-model:product = "selectedProduct"
      @product-data="updateProduct"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:product="selectedProduct"
      @product-data="deleteProduct"
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
