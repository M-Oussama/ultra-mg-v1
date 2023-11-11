<!-- eslint-disable vue/no-mutating-props -->
<script setup>
import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'

const props = defineProps({
  id: {
    type: Number,
    required: true,
  },
  data: {
    type: Object,
    required: true,
    default: () => ({
      id: '',
      name: '',
      brand: '',
      description: '',
      product_code: '',
      SKU: '',
      price: 15,
      stockable: false,
      tax_rate: 0.5,
      product_stock:{
        quantity:0
      }
    }),
  },
})

const emit = defineEmits([
  'removeProduct',
  'totalAmount',
])

const totalPrice = computed(() => Number(props.data.price) * Number(props.data.quantity))


const removeProduct = () => {
  emit('removeProduct', props.data)
}
const totalAmount = () => {
  emit('totalAmount', props.data)
}

watch(totalPrice, () => {

  console.log("watch price")
  totalAmount()
}, { immediate: true })



</script>

<template>
  <!-- eslint-disable vue/no-mutating-props -->

  <div class="add-products-header mb-2 d-none d-md-flex">
    <VRow class="font-weight-medium px-4">
      <VCol
        cols="12"
        md="6"
      >
        <span class="text-sm">
          Item
        </span>
      </VCol>
      <VCol
        cols="12"
        md="2"
      >
        <span class="text-sm">
          Price
        </span>
      </VCol>
      <VCol
        cols="12"
        md="2"
      >
        <span class="text-sm">
          Quantity
        </span>
      </VCol>
      <VCol
        cols="12"
        md="2"
      >
        <span class="text-sm">
          Total
        </span>
      </VCol>
    </VRow>
  </div>
  <VCard
    flat
    border
    class="d-flex flex-row"
  >
    <!-- 👉 Left Form -->
    <div class="pa-5 flex-grow-1">
      <VRow>


          <VCol
            cols="12"
            md="6"
            sm="4"
          >
            <VTextField
              v-model="props.data.product.name"
              type="string"
              label="Product"
              disabled
            />




        </VCol>
        <VCol
          cols="12"
          md="2"
          sm="4"
        >
          <VTextField
            v-model="props.data.price"
            type="number"
            label="Price"
          />


        </VCol>
        <VCol
          cols="12"
          md="2"
          sm="4"
        >
          <VTextField
            v-model="props.data.quantity"
            type="number"
            label="Quantity"
          />
        </VCol>
        <VCol
          cols="12"
          md="2"
          sm="4"
        >
          <p class="text-sm-center my-2">
            <span class="d-inline d-md-none">Price: </span>
            <span class="text-body-1">{{ totalPrice.toFixed(2) }} DZD</span>
          </p>
        </VCol>
      </VRow>
    </div>

    <!-- 👉 Item Actions -->
    <div class="d-flex flex-column justify-space-between border-s pa-1">
      <VBtn
        icon
        size="x-small"
        color="default"
        variant="text"
        @click="removeProduct"

      >
        <VIcon
          size="20"
          icon="tabler-x"
        />
      </VBtn>

    </div>
  </VCard>
</template>

<style>
select option:first-child {
  font-size: 7pt;
}
.ui.dropdown {
  font-size: large;
}
.ui.dropdown .menu>.item {
  font-size: large;
}
.ui.search.selection.dropdown>input.search {
  font-size: large;
}
.v-card {
  position: unset;
  overflow: visible;
}
.selection> div {
  color: #000 !important;
}
</style>
