<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

import {useSaleStore} from "@/views/apps/POS/sales/useSaleStore";
import {successMiddleware} from "@/middlewares/successMiddleware";
import {errorsMiddleware} from "@/middlewares/errorsMiddleware";

import "vue-search-select/dist/VueSearchSelect.css"
import { ModelListSelect } from 'vue-search-select'
const props = defineProps({
  isDrawerOpen: {
    type: null,
    required: true,
  },
  benefit:{
    type: Object,
    required:true
  },

  loading:{
    type:null,
    required: true
  }
})
const isFormValid = ref(false)
const refForm = ref()
const saleStore = useSaleStore()
const benefit = ref({
  month:null,
  year:null,
  electricity:0,
  other_charges:0,
  employee_salary:0

})
watch(() => props.benefit, (newObject) => {
  if(newObject){
    benefit.value = {...newObject};
  }


})

const data = ref();
const client = ref({
  id:-1,
})
const emit = defineEmits([
  'benefitData',
])
// 👉 drawer close
const closeNavigationDrawer = () => {
  props.isDrawerOpen.open = false;
}
const onSubmit = () => {

console.log("benefit")
console.log(benefit.value)
    handleDrawerModelValueUpdate()
    props.loading.isActive = true;
    saleStore.updateBenefit(benefit.value).then(response => {
      props.loading.isActive = false;
      successMiddleware('Benefit updated Successfully')
      emit('benefitData', {
        data
      })
    }).catch(error => {
      console.log(error)
      props.loading.isActive = false;
      errorsMiddleware(
        "Error Occured",
        "Oops! Looks like the Benefit has not been submited "
      )
    })

}

const fullName = item => {
  return `${item.name}  ${item.surname}`
}

const handleDrawerModelValueUpdate = val => {

  console.log(val);
  props.isDrawerOpen.open = false;
}

</script>

<template>
  <VNavigationDrawer
    temporary
    :width="400"
    location="end"
    class="scrollable-content"
    :model-value="props.isDrawerOpen.open"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Title -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        Edit Benefit
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
                  v-model="benefit.electricity"
                  type="number"
                  label="Electricity"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="benefit.employee_salary"
                  type="number"
                  label="employee salary"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="benefit.other_charges"
                  type="number"
                  label="other charges"
                />
              </VCol>
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

<style>

</style>
