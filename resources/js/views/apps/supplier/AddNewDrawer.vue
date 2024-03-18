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
  cities: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'createData',
])

const isFormValid = ref(false)
const refForm = ref()

const data = ref({
  name:'',
  surname:'',
  email:'',
  address:'',
  phone:'',
  NRC:'',
  NIF:'',
  NART:'',
  NIS:'',
  city_id:'',
});

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

      emit('createData', {
        name: data.value.name,
        surname: data.value.surname,
        email: data.value.email,
        address: data.value.address,
        phone: data.value.phone,
        NRC: data.value.NRC,
        NIS: data.value.NIS,
        NIF: data.value.NIF,
        NART: data.value.NART,
        city_id: data.value.city_id,
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
const printName = (city) => {
  console.log(city);
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
        Add New
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
                  v-model="data.name"
                  :rules="[requiredValidator]"
                  label="Name"
                />
              </VCol>
              <!-- 👉 surname -->
              <VCol cols="12">
                <VTextField
                  v-model="data.surname"

                  label="Surname"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <VTextField
                  v-model="data.email"
                  :rules="[emailValidator]"

                  label="Email"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol cols="12">


                <VAutocomplete
                  clearable
                  v-model="data.city_id"
                  :items="props.cities"
                  item-value="id"
                  item-title="name"
                  label="City"
                  :rules="[requiredValidator]"
                />

              </VCol>
              <VCol cols="12">
                <VTextField
                  v-model="data.address"
                  :rules="[requiredValidator]"
                  label="Address"
                />
              </VCol>
              <!-- 👉 Phone -->
              <VCol cols="12">
                <VTextField
                  v-model="data.phone"

                  label="Phone"
                />
              </VCol>

              <!-- 👉 RC -->
              <VCol cols="12">
                <VTextField
                  v-model="data.NRC"

                  label="Numéro de registre"
                />
              </VCol>
              <!-- 👉 NIF -->
              <VCol cols="12">
                <VTextField
                  v-model="data.NIF"

                  label="Numéro de NIF"
                />
              </VCol>
              <!-- 👉 NIS -->
              <VCol cols="12">
                <VTextField
                  v-model="data.NIS"

                  label="Numéro de NIS"
                />
              </VCol>
              <!-- 👉 NART -->
              <VCol cols="12">
                <VTextField
                  v-model="data.NART"

                  label="Numéro de ARTICLE"
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
