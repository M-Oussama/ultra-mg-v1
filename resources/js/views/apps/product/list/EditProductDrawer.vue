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
  product: {
    type: Object,

  },
})

// 👉 Watch for changes in the Client prop and update form fields
watch(() => props.product, (newProduct) => {
  console.log(newProduct);
  if (newProduct) {
      id.value =  newProduct.id || 0;
      name.value = newProduct.name || '';
      brand.value = newProduct.brand|| '';
      description.value = newProduct.description|| '';
      product_code.value = newProduct.product_code|| '';
      sku.value = newProduct.SKU|| '';
      min_stock_level.value = newProduct.min_stock_level || 0;
      price.value = newProduct.price|| 0;
      stockable.value = newProduct.stockable|| 0;
      tax_rate.value = newProduct.tax_rate|| 0;
  }
});

const emit = defineEmits([
  'update:isDrawerOpen',
  'productData',
])

const isFormValid = ref(false)
const refForm = ref()
const id = ref()
const name = ref('')
const brand = ref('')
const description = ref('')
const product_code = ref('')
const sku = ref('')
const min_stock_level = ref(0)
let price = ref(0)
let stockable = ref(0)
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
        id: id.value,
        name : name.value,
        brand: brand.value,
        description: description.value,
        product_code: product_code.value,
        SKU: sku.value,
        min_stock_level: min_stock_level.value,
        price: price.value,
        stockable: !!stockable.value,
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

  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Edit Product
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
