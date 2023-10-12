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
  client: {
    type: Object,
    required: true,
  },
})

// 👉 Watch for changes in the Client prop and update form fields
watch(() => props.client, (newClient) => {
  console.log(newClient)
  if (newClient) {
    id.value = newClient.id || 0
    name.value = newClient.name || '';
    surname.value = newClient.surname || '';
    email.value = newClient.email || '';
    address.value = newClient.address || '';
    phone.value = newClient.phone || '';
    NRC.value = newClient.NRC || '';
    NIF.value = newClient.NIF || '';
    NART.value = newClient.NART || '';
    NIS.value = newClient.NIS || '';
  }
});

const emit = defineEmits([
  'update:isDrawerOpen',
  'clientData',
])

const isFormValid = ref(false)
const refForm = ref()
const id = ref()
const name = ref('')
const surname = ref('')
const email = ref('')
const address = ref('')
const phone = ref('')
const NRC = ref('')
const NIF = ref('')
const NIS = ref('')
const NART = ref('')
const company = ref('')
const country = ref('')
const contact = ref('')
const role = ref()
let password = ref()
let visible = ref(false)
let confirm_visible = ref(false)
let isPasswordVisible = ref(false)
let confirm_password = ref()
const plan = ref()
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
      emit('clientData', {
        id: id.value,
        name: name.value,
        surname: surname.value,
        email: email.value,
        address: address.value,
        phone: phone.value,
        NRC: NRC.value,
        NIS: NIS.value,
        NIF: NIF.value,
        NART: NART.value,
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
                  v-model="name"
                  :rules="[requiredValidator]"
                  label="Name"
                />
              </VCol>
              <!-- 👉 surname -->
              <VCol cols="12">
                <VTextField
                  v-model="surname"
                  :rules="[requiredValidator]"
                  label="Surname"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <VTextField
                  v-model="email"
                  :rules="[requiredValidator, emailValidator]"
                  label="Email"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol cols="12">
                <VTextField
                  v-model="address"
                  :rules="[requiredValidator]"
                  label="Address"
                />
              </VCol>
              <!-- 👉 Phone -->
              <VCol cols="12">
                <VTextField
                  v-model="phone"
                  :rules="[requiredValidator]"
                  label="Phone"
                />
              </VCol>

              <!-- 👉 RC -->
              <VCol cols="12">
                <VTextField
                  v-model="NRC"
                  :rules="[requiredValidator]"
                  label="Numéro de registre"
                />
              </VCol>
              <!-- 👉 NIF -->
              <VCol cols="12">
                <VTextField
                  v-model="NIF"
                  :rules="[requiredValidator]"
                  label="Numéro de NIF"
                />
              </VCol>
              <!-- 👉 NIS -->
              <VCol cols="12">
                <VTextField
                  v-model="NIS"
                  :rules="[requiredValidator]"
                  label="Numéro de NIS"
                />
              </VCol>
              <!-- 👉 NART -->
              <VCol cols="12">
                <VTextField
                  v-model="NART"
                  :rules="[requiredValidator]"
                  label="Numéro de ARTICLE"
                />
              </VCol>

              <!--              &lt;!&ndash; 👉 company &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="company"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="Company"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 Country &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="country"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="Address"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 Contact &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="contact"-->
              <!--                  type="number"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="Phone number"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 Plan &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VSelect-->
              <!--                  v-model="plan"-->
              <!--                  label="Select Plan"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  :items="['Basic', 'Company']"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 N°RC &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="country"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="N°RC"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 N°IS &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="country"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="N°IS"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 N°IF &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="country"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="N°IF"-->
              <!--                />-->
              <!--              </VCol>-->

              <!--              &lt;!&ndash; 👉 N°ART &ndash;&gt;-->
              <!--              <VCol cols="12">-->
              <!--                <VTextField-->
              <!--                  v-model="country"-->
              <!--                  :rules="[requiredValidator]"-->
              <!--                  label="N°ART"-->
              <!--                />-->
              <!--              </VCol>-->


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
