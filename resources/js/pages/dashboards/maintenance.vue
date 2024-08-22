<script setup>
import { useTheme } from 'vuetify'
import {useDashboardStore} from "@/views/dashboards/maintenance/useDashboardStore";
import AnalyticsIncomingMaintenanceTable from "@/views/dashboards/maintenance/AnalyticsIncomingMaintenanceTable.vue";
import AnalyticsRecentMaintenanceTable from "@/views/dashboards/maintenance/AnalyticsRecentMaintenanceTable.vue";
import { nextTick } from 'vue';

const vuetifyTheme = useTheme()
const currentTheme = vuetifyTheme.current.value.colors



const dashboard = useDashboardStore()
const loading2 = ref({
  isActive :false
})

const incomingMaintenancesList = ref([])
const maintenancesList = ref([])
const getMaintenanceDashboard = () => {
  loading2.value.isActive = true;
  dashboard.getMaintenanceDashboard().then(response => {
    maintenancesList.value = response.data.maintenances
    incomingMaintenancesList.value = response.data.incomingMaintenances
    loading2.value.isActive = false;

  }).catch(error => {
    loading2.value.isActive = false;
  })
}

nextTick(() => {
  // This runs after the DOM has been updated with the new value
  getMaintenanceDashboard()
});
</script>

<template>
  <VRow class="match-height">
    <!-- 👉 Website analytics -->
    <v-overlay
      :model-value="loading2.isActive"
      class="align-center justify-center"
    >
      <v-progress-circular
        color="primary"
        indeterminate
        size="64"
      ></v-progress-circular>
    </v-overlay>

<!--    <VCol-->
<!--      cols="12"-->
<!--      md="6"-->
<!--    >-->
<!--      <AnalyticsMaintenanceAnalytics :loading = "loading" />-->
<!--    </VCol>-->





    <!-- 👉 Earning Reports Weekly Overview -->
    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsIncomingMaintenanceTable :maintenancesList="incomingMaintenancesList"  />

    </VCol>

    <!-- 👉 Support Tracker -->
    <VCol
      cols="12"
      md="6"
    >
      <!-- -->
      <AnalyticsRecentMaintenanceTable :maintenancesList="maintenancesList"/>


    </VCol>








  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/libs/apex-chart.scss";
</style>

<route lang="yaml">
meta:
  action: maintenance
  subject: dashboard
</route>
