<script setup>
import PERMISSIONS from '@/router/permissions'
import { useProductImportStore } from '@/views/apps/product/useProductImportStore'

const store = useProductImportStore()
const selectedFile = ref(null)
const departmentId = ref('')
const uploading = ref(false)
const result = ref(null)
const apiError = ref('')

const onFileChange = files => {
  if (Array.isArray(files))
    selectedFile.value = files[0] || null
  else
    selectedFile.value = files || null

  result.value = null
  apiError.value = ''
}

const handleImport = async () => {
  if (!selectedFile.value) {
    apiError.value = 'Please select a CSV file first.'
    return
  }

  if (!departmentId.value) {
    apiError.value = 'Please enter department_id.'
    return
  }

  uploading.value = true
  apiError.value = ''
  result.value = null

  try {
    const response = await store.importCsv(selectedFile.value, departmentId.value)
    result.value = response.data
  } catch (error) {
    apiError.value = error?.response?.data?.message || 'Import failed.'
  } finally {
    uploading.value = false
  }
}
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12" md="9" lg="8">
        <VCard title="Import Products CSV">
          <VCardText class="pt-4">
            <VTextField
              v-model="departmentId"
              label="Department ID (applied to all rows)"
              type="number"
              class="mb-4"
            />

            <p class="mb-4 text-body-2">
              Required columns: <strong>id</strong>, <strong>name</strong>, <strong>brand</strong>, <strong>description</strong>, <strong>product_code</strong>, <strong>category_id</strong>, <strong>SKU</strong>, <strong>min_stock_level</strong>, <strong>price</strong>, <strong>weight</strong>, <strong>stockable</strong>, <strong>tax_rate</strong>.
            </p>

            <VFileInput
              accept=".csv,text/csv"
              label="CSV File"
              prepend-icon="tabler-file-upload"
              @update:model-value="onFileChange"
            />

            <div class="d-flex mt-4">
              <VBtn
                :loading="uploading"
                :disabled="uploading || !$can(PERMISSIONS.PRODUCT.ADD, PERMISSIONS.PRODUCT.SUBJECT)"
                @click="handleImport"
              >
                Upload and Import
              </VBtn>
            </div>

            <VAlert v-if="apiError" type="error" variant="tonal" class="mt-4">
              {{ apiError }}
            </VAlert>

            <VAlert v-if="result" type="success" variant="tonal" class="mt-4">
              Inserted: {{ result.inserted }} | Failed: {{ result.failed }}
            </VAlert>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </section>
</template>

<route lang="yaml">
meta:
  action: add
  subject: products
</route>

