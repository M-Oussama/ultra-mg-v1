<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import {
  emailValidator,
  requiredValidator,
} from '@validators'
import {onMounted} from "vue";

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,

  },
  employee: {
    type: Object,
    required: true,
  },
  cities:{
    type:Object
  }

})
const emit = defineEmits([
  'update:isDrawerOpen',
  'employeeData',
])


const isFormValid = ref(false)
const refForm = ref()
const employee = ref({
  name:'',
  name_ar:'',
  surname:'',
  surname_ar:'',
  birthdate:'',
  birthplace:'',
  email:'',
  address:'',
  phone:'',
  NIN:'',
  NCN:'',
  CNAS:'',
  card_issue_date:'',
  card_issue_place:'',
  father_name_ar: '',
  mother_full_name_ar: '',
  birth_city_id:  '',
  card_issued_city_id:  '',
})
watch(() => props.employee, (newEmployee) => {
    if(newEmployee){
      employee.value = {...newEmployee};
    }


})



const status = ref()

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
      emit('employeeData', {
        employee
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
        Edit Client
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
              <!-- 👉  name -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.name"
                  :rules="[requiredValidator]"
                  label="Name"
                />
              </VCol>
              <!-- 👉 surname -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.surname"
                  :rules="[requiredValidator]"
                  label="Surname"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="employee.name_ar"
                  :rules="[requiredValidator]"
                  label="Name Arabic"
                  dir="rtl"
                  lang="ar"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="employee.surname_ar"
                  :rules="[requiredValidator]"
                  label="Surname Arabic"
                  dir="rtl"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="employee.father_name_ar"
                  :rules="[requiredValidator]"
                  dir="rtl"
                  label="Father's name Arabic"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="employee.mother_full_name_ar"
                  dir="rtl"
                  :rules="[requiredValidator]"
                  label="Mother's Full name Arabic"
                />
              </VCol>
              <VCol cols="12">
                <AppDateTimePicker
                  v-model="employee.birthdate"
                  label="la date de naissance"
                />
              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="employee.birthplace"
                  :rules="[requiredValidator]"
                  label="lieu de naissance"
                />
              </VCol>

              <VCol cols="12">
                <VAutocomplete
                  clearable
                  v-model="employee.birth_city_id"
                  :items="props.cities"
                  item-value="id"
                  item-title="name"
                  label="City"
                  :rules="[requiredValidator]"
                />
              </VCol>
              <!-- 👉 Email -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.address"
                  :rules="[requiredValidator]"
                  label="Address"
                />
              </VCol>
              <!-- 👉 Phone -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.phone"
                  :rules="[requiredValidator]"
                  label="Phone"
                />
              </VCol>

              <!-- 👉 RC -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.NIN"
                  :rules="[requiredValidator]"
                  label="Numéro d'identification nationale"
                />
              </VCol>
              <!-- 👉 NIF -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.NCN"
                  :rules="[requiredValidator]"
                  label="Numéro de carte nationale"
                />
              </VCol>
              <VCol cols="12">
                <AppDateTimePicker
                  v-model="employee.card_issue_date"
                  label="card issue date"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="employee.card_issue_place"
                  :rules="[requiredValidator]"
                  label="card issue place"
                />
              </VCol>
              <VCol cols="12">
                <VAutocomplete
                  clearable
                  v-model="employee.card_issued_city_id"
                  :items="props.cities"
                  item-value="id"
                  item-title="name"
                  label="City"
                  :rules="[requiredValidator]"
                />
              </VCol>
              <!-- 👉 NIS -->
              <VCol cols="12">
                <VTextField
                  v-model="employee.CNAS"
                  :rules="[requiredValidator]"
                  label="Numéro de CNAS"
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
