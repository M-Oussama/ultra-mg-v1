<script setup>
import PERMISSIONS from '@/router/permissions'
import { useCertifyInvoiceImportStore } from '@/views/apps/certifyInvoice/useCertifyInvoiceImportStore'

const certifyInvoiceImportStore = useCertifyInvoiceImportStore()
const selectedFile = ref(null)
const uploading = ref(false)
const importProgress = ref(0)
let progressTimer = null
const result = ref(null)
const apiError = ref('')

const stopProgressTimer = () => {
  if (progressTimer) {
    clearInterval(progressTimer)
    progressTimer = null
  }
}

const startProgressTimer = () => {
  stopProgressTimer()
  progressTimer = setInterval(() => {
    if (importProgress.value < 92)
      importProgress.value += 1
  }, 450)
}

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

  uploading.value = true
  importProgress.value = 1
  apiError.value = ''
  result.value = null
  startProgressTimer()

  try {
    const response = await certifyInvoiceImportStore.importCsv(selectedFile.value, event => {
      if (!event.total)
        return

      // The request can spend most of its time processing rows after upload.
      // Cap the transport progress and let the waiting state continue visibly.
      importProgress.value = Math.max(
        importProgress.value,
        Math.min(35, Math.round((event.loaded / event.total) * 35)),
      )
    })

    importProgress.value = 100
    result.value = response.data
  } catch (error) {
    apiError.value = error?.response?.data?.message || 'Import failed.'
  } finally {
    stopProgressTimer()
    uploading.value = false
  }
}

onBeforeUnmount(stopProgressTimer)
</script>

<template>
  <section>
    <VRow>
      <VCol cols="12" md="9" lg="8">
        <VCard title="Import Certify Invoices CSV">
          <VCardText class="pt-4">
            <p class="mb-4 text-body-2">
              Required columns: <strong>id</strong>, <strong>fac_id</strong>, <strong>date</strong>,
              <strong>client_id</strong>, <strong>amount</strong>, <strong>custom_cheque_number</strong>, <strong>cheque_id</strong>, <strong>payment_type</strong>.
            </p>
            <p class="mb-4 text-body-2">
              Payment type values: 1=Espece, 2=Cheque, 3=Virement Bancaire, 4=Versement Espece.
            </p>

            <VFileInput
              accept=".csv,text/csv"
              label="CSV File"
              prepend-icon="tabler-file-upload"
              @update:model-value="onFileChange"
            />

            <div
              v-if="uploading"
              class="mt-4"
            >
              <div class="d-flex align-center justify-space-between mb-2">
                <span class="text-body-2">Importing certify invoices…</span>
                <span class="text-body-2 font-weight-medium">{{ importProgress }}%</span>
              </div>
              <VProgressLinear
                :model-value="importProgress"
                color="dark"
                height="8"
                rounded
              />
              <p class="text-caption text-medium-emphasis mt-2 mb-0">
                Please keep this page open while the records are being processed.
              </p>
            </div>

            <div
              v-else
              class="d-flex mt-4"
            >
              <VBtn
                :disabled="!$can(PERMISSIONS.CERTIFY_INVOICE.ADD, PERMISSIONS.CERTIFY_INVOICE.SUBJECT)"
                @click="handleImport"
              >
                Upload and Import
              </VBtn>
            </div>

            <VAlert
              v-if="apiError"
              type="error"
              variant="tonal"
              class="mt-4"
            >
              {{ apiError }}
            </VAlert>

            <VAlert
              v-if="result"
              type="success"
              variant="tonal"
              class="mt-4"
            >
              Inserted: {{ result.inserted }} | Failed: {{ result.failed }}
            </VAlert>

            <VTable
              v-if="result?.errors?.length"
              class="text-no-wrap mt-4"
            >
              <thead>
                <tr>
                  <th>Row</th>
                  <th>Errors</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, index) in result.errors"
                  :key="index"
                >
                  <td>{{ item.row }}</td>
                  <td>{{ item.errors.join(', ') }}</td>
                </tr>
              </tbody>
            </VTable>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </section>
</template>

<route lang="yaml">
meta:
  action: add
  subject: certify_invoices
</route>
