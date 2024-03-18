<script setup>
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";
import {useSupplierController} from "@/views/apps/supplier/useSupplierController";
import AddNewDrawer from '@/views/apps/supplier/AddNewDrawer.vue'
import EditDrawer from '@/views/apps/supplier/EditDrawer.vue'
import ConfirmationDialog from '@/views/apps/supplier/ConfirmationDialog.vue'
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";
import {successMiddleware} from "@/middlewares/successMiddleware";


/**
 *  INSTANTES
 */

const pageName = "Suppliers"

const instantName = "Supplier"

const objects = ref([]);

const selectedObject = ref();

const cities = ref([]);

const loading = ref(false);

const currentTab = 0;

const supplierController = useSupplierController()

const isAddNewVisible = ref(false);

const isEditVisible = ref(false);

const isDialogVisible = ref(false);

const rowPerPage = ref(10)

const currentPage = ref(1)

const totalPage = ref(1)

const totalObjects = ref(0)

// 👉 Fetching Clients
const getList = () => {
  loading.value = true;
  supplierController.getSuppliers({
    perPage: rowPerPage.value,
    currentPage: currentPage.value,
  }).then(response => {
    objects.value = response.data.suppliers.data
    cities.value = response.data.cities
    totalPage.value = response.data.totalPage
    totalObjects.value = response.data.total
    loading.value = false
  }).catch(error => {
     loading.value = false;
     errorsMiddleware(error.code, error.response.statusText)
  })
}
//
watchEffect(getList)


const create = (createData) => {
  loading.value = true;
  supplierController.createSupplier(createData).then(response => {
    getList();
    successMiddleware(response.data.message)
  }).catch(error => {
    loading.value = false;
    errorsMiddleware(error.code, error.response.statusText)

  })

}

const update = (updateData) => {
  loading.value = true;
  supplierController.updateSupplier(updateData).then(response => {
    getList();
    successMiddleware(response.data.message)
  }).catch(error => {
    loading.value = false;
    errorsMiddleware(error.code, error.response.statusText)

  })
}

const destroy = (objectData) => {
  console.log(objectData)
  loading.value = true;
  supplierController.deleteSupplier(objectData).then(response => {
    getList();
    successMiddleware(response.data.message)
  }).catch(error => {
    loading.value = false;
    errorsMiddleware(error.code, error.response.statusText)

  })
}

// 👉 watching current page
watchEffect(() => {
  if (currentPage.value > totalPage.value)
    currentPage.value = totalPage.value
})

// 👉 Computing pagination data
const paginationData = computed(() => {
  const firstIndex = objects.value.length ? (currentPage.value - 1) * rowPerPage.value + 1 : 0
  const lastIndex = objects.value.length + (currentPage.value - 1) * rowPerPage.value

  return `Showing ${ firstIndex } to ${ lastIndex } of ${ totalObjects.value } entries`
})

</script>

<template>
  <section>
    <v-overlay
      :model-value="loading"
      class="align-center justify-center"
    >
      <v-progress-circular
        color="primary"
        indeterminate
        size="64"
      ></v-progress-circular>
    </v-overlay>
    <!-- Dialog Content -->
    <VCard :title="pageName">


      <VCardText class="d-flex justify-end gap-3 flex-wrap">

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
<!--            <v-progress-circular-->
<!--              v-if="loading"-->
<!--              indeterminate-->
<!--              color="primary"-->
<!--            ></v-progress-circular>-->
            <VTextField

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
            @click="isAddNewVisible = true"
          >
            New {{ instantName }}
          </VBtn>
        </div>

        <div
        style="width: 100%"

        >
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
                ACTIONS
              </th>
            </tr>
            </thead>
            <!-- 👉 table body -->
            <tbody>
            <tr
              v-for="object in objects"
              :key="object.id"
              style="height: 3.75rem;"
            >
              <!-- 👉 ID -->
              <td>
                {{object.id}}
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
                      v-if="object.avatar"
                      :src="object.avatar"
                    />
                    <span v-else>{{ object.name.toUpperCase().charAt(0) }} </span>
                  </VAvatar>

                  <div class="d-flex flex-column">
                    <h6 class="text-base">
                      <RouterLink
                        :to="{ name: 'apps-user-view-id', params: { id: object.id } }"
                        class="font-weight-medium user-list-name"
                      >
                        {{ object.name }}
                      </RouterLink>
                    </h6>
                    <span class="text-sm text-disabled">@{{ object.email }}</span>
                  </div>
                </div>
              </td>

              <!-- 👉 email -->
              <td>
                <span class="text-capitalize text-base font-weight-semibold">{{ object.city.name }}</span>
              </td>
              <td>
                <span class="text-capitalize text-base font-weight-semibold">{{ object.phone }}</span>
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
                  :to="{ name: 'apps-client-log-id', params: { id: object.id } }"

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
                  @click="selectedObject = {...object}; isEditVisible = true"

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
                  @click="selectedObject = {...object}; isDialogVisible = true"
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
            <tfoot v-show="!objects.length">
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



        </div>
        <VDivider />
        <div >
            <span class="text-sm mt-5 text-disabled">
              {{ paginationData }}
            </span>

          <VPagination
            v-model="currentPage"
            size="small"
            :total-visible="5"
            :length="totalPage"
          />

        </div>
      </VCardText>
    </VCard>
    <AddNewDrawer
      v-model:isDrawerOpen="isAddNewVisible"
      v-model:cities="cities"
      @create-data="create"
    />
    <EditDrawer
      v-model:isDrawerOpen="isEditVisible"
      v-model:cities="cities"
      v-model:data="selectedObject"
      @update-data="update"
    />

    <ConfirmationDialog
      v-model:isDialogVisible="isDialogVisible"
      v-model:object="selectedObject"
      @object-data="destroy"
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
