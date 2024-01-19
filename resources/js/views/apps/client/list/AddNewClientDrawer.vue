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
  'clientData',
])

const isFormValid = ref(false)
const refForm = ref()
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
const city_id = ref(null)
const role = ref()
let password = ref()
let visible = ref(false)
let confirm_visible = ref(false)
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
        name: name.value,
        surname: surname.value,
        email: email.value,
        address: address.value,
        phone: phone.value,
        NRC: NRC.value,
        NIS: NIS.value,
        NIF: NIF.value,
        NART: NART.value,
        city_id: city_id.value,
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
  emit('update:z', val)
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
        Add Client
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

                  label="Surname"
                />
              </VCol>

              <!-- 👉 Email -->
              <VCol cols="12">
                <VTextField
                  v-model="email"
                  :rules="[emailValidator]"

                  label="Email"
                />
              </VCol>

              <!-- 👉 Address -->
              <VCol cols="12">


                <VAutocomplete
                  clearable
                  v-model="city_id"
                  :items="props.cities"
                  item-value="id"
                  item-title="name"
                  label="City"
                  :rules="[requiredValidator]"
                />

              </VCol>
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

                  label="Phone"
                />
              </VCol>

              <!-- 👉 RC -->
              <VCol cols="12">
                <VTextField
                  v-model="NRC"

                  label="Numéro de registre"
                />
              </VCol>
              <!-- 👉 NIF -->
              <VCol cols="12">
                <VTextField
                  v-model="NIF"

                  label="Numéro de NIF"
                />
              </VCol>
              <!-- 👉 NIS -->
              <VCol cols="12">
                <VTextField
                  v-model="NIS"

                  label="Numéro de NIS"
                />
              </VCol>
              <!-- 👉 NART -->
              <VCol cols="12">
                <VTextField
                  v-model="NART"

                  label="Numéro de ARTICLE"
                />
              </VCol>

<!--              &lt;!&ndash; 👉 Password &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  hide-details="auto"-->
<!--                  :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"-->
<!--                  :type="visible ? 'text' : 'password'"-->
<!--                  density="compact"-->
<!--                  prepend-inner-icon="mdi-lock-outline"-->
<!--                  variant="outlined"-->
<!--                  @click:append-inner="visible = !visible"-->
<!--                  v-model="password"-->
<!--                  :rules="[requiredValidator]"-->
<!--                  label="password"-->
<!--                />-->
<!--              </VCol> -->
<!--              -->
<!--              &lt;!&ndash; 👉 Confirm Password &ndash;&gt;-->
<!--              <VCol cols="12">-->
<!--                <VTextField-->
<!--                  hide-details="auto"-->
<!--                  :append-inner-icon="confirm_visible ? 'mdi-eye-off' : 'mdi-eye'"-->
<!--                  :type="confirm_visible ? 'text' : 'password'"-->
<!--                  density="compact"-->
<!--                  prepend-inner-icon="mdi-lock-outline"-->
<!--                  variant="outlined"-->
<!--                  @click:append-inner="confirm_visible = !confirm_visible"-->
<!--                  v-model="confirm_password"-->
<!--                  :rules="[requiredValidator, v => !!v || 'Confirm password is required', v => v === password || 'Passwords do not match']"-->
<!--                  label="Confirm Password"-->

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
