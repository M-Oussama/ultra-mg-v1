<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,

  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'productData',
])

const isFormValid = ref(false)
const refForm = ref()
const name = ref('')
const brand = ref('')
const description = ref('')
const product_code = ref('')
const sku = ref('')
const min_stock_level = ref(0)
let price = ref(0)
let stockable = ref(false)
const tax_rate = ref(0.00)
// 👉 drawer close
const closeNavigationDrawer = () => {
  emit('update:isDrawerOpen', false)
  nextTick(() => {
    refForm.value?.reset()
    refForm.value?.resetValidation()
  })
}

const onSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      emit('productData', {
        name : name.value,
        brand: brand.value,
        description: description.value,
        product_code: product_code.value,
        SKU: sku.value,
        min_stock_level: min_stock_level.value,
        price: price.value,
        stockable: stockable.value,
        tax_rate: tax_rate.value,
      })
      emit('update:isDrawerOpen', false)
      nextTick(() => {
        refForm.value?.reset()
        refForm.value?.resetValidation()
      })
    }
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Add User
      </h6>

      <VSpacer />

      <!-- 👉 Close btn -->
      <VBtn
        variant="tonal"
        color="default"
        icon
        size="32"
        class="rounded"
        @click="closeNavigationDrawer"
      >
        <VIcon
          size="18"
          icon="tabler-x"
        />
      </VBTn>
    </div>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- 👉 Form -->
          <VForm
            ref="refForm"
            v-model="isFormValid"
            @submit.prevent="onSubmit"
          >
            <VRow>
              <!-- 👉 name -->
              <VCol cols="12">
                <VTextField
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Product Name"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="brand"

                  label="Product Brand"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="description"

                  label="Product description"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="product_code"

                  label="Product code"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="sku"

                  label="Product SKU"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="min_stock_level"
                  :rules="[requiredValidator]"
                  label="min stock level"
                  type="number"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="price"
                  :rules="[requiredValidator]"
                  label="Price"
                  type="number"
                />
              </VCol>
              <VCol cols="12" >
                <VSwitch
                  v-model="stockable"
                  :false-value="0"
                  :true-value="1"
                  inset
                  :label="`Stockable`"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="tax_rate"
                  type="number"
                  :rules="[requiredValidator]"
                  label="Tax Rate"
                />
              </VCol>

              <!-- 👉 Submit and Cancel -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Submit
                </VBtn>
                <VBtn
                  type="reset"
                  variant="tonal"
                  color="secondary"
                  @click="closeNavigationDrawer"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
