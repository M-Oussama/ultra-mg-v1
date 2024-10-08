<script setup>
import { useTheme } from 'vuetify'
import AnalyticsEarningReportsWeeklyOverview from '@/views/dashboards/analytics/AnalyticsEarningReportsWeeklyOverview.vue'
import AnalyticsMonthlyCampaignState from '@/views/dashboards/analytics/AnalyticsMonthlyCampaignState.vue'
import AnalyticsSalesByCountries from '@/views/dashboards/analytics/AnalyticsSalesByCountries.vue'
import AnalyticsSalesOverview from '@/views/dashboards/analytics/AnalyticsSalesOverview.vue'
import AnalyticsSourceVisits from '@/views/dashboards/analytics/AnalyticsSourceVisits.vue'
import AnalyticsSupportTracker from '@/views/dashboards/analytics/AnalyticsSupportTracker.vue'
import AnalyticsTotalEarning from '@/views/dashboards/analytics/AnalyticsTotalEarning.vue'
import AnalyticsWebsiteAnalytics from '@/views/dashboards/analytics/AnalyticsWebsiteAnalytics.vue'
import CardStatisticsVertical from '@core/components/CardStatisticsVertical.vue'
import AnalyticsVacationTable from "@/views/dashboards/analytics/AnalyticsVacationTable.vue";
import {useDashboardStore} from "@/views/dashboards/analytics/useDashboardStore";
import AnalyticsIncomingVacationTable from "@/views/dashboards/analytics/AnalyticsIncomingVacationTable.vue";
import {nextTick} from "vue";

const vuetifyTheme = useTheme()
const currentTheme = vuetifyTheme.current.value.colors
const loading2 = ref({
  isActive: false
})
const statisticsVertical = {
  title: 'Revenue Generated',
  color: 'success',
  icon: 'tabler-credit-card',
  stats: '97.5k',
  height: 120,
  series: [{
    data: [
      300,
      350,
      330,
      380,
      340,
      400,
      380,
    ],
  }],
  chartOptions: {
    chart: {
      height: 110,
      type: 'area',
      parentHeightOffset: 0,
      toolbar: { show: false },
      sparkline: { enabled: true },
    },
    tooltip: { enabled: false },
    markers: {
      colors: 'transparent',
      strokeColors: 'transparent',
    },
    grid: { show: false },
    colors: [currentTheme.success],
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 0.8,
        opacityFrom: 0.6,
        opacityTo: 0.1,
      },
    },
    dataLabels: { enabled: false },
    stroke: {
      width: 2,
      curve: 'smooth',
    },
    xaxis: {
      show: true,
      lines: { show: false },
      labels: { show: false },
      stroke: { width: 0 },
      axisBorder: { show: false },
    },
    yaxis: {
      stroke: { width: 0 },
      show: false,
    },
  },
}

const dashboard = useDashboardStore()
const incomingVacationList = ref([])
const vacationList = ref([])

const getAdminDashboard = () => {
  loading2.value.isActive = true;
  dashboard.getAdminDashboard().then(response => {
    vacationList.value = response.data.vacationList
    incomingVacationList.value = response.data.incomingVacationList

    loading2.value.isActive = false;

  }).catch(error => {
    loading2.value.isActive = false;
  })
}
nextTick(() => {
  // This runs after the DOM has been updated with the new value
  getAdminDashboard()
});
</script>

<template>
  <VRow class="match-height">
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
    <!-- 👉 Website analytics -->
    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsWebsiteAnalytics />
    </VCol>

    <!-- 👉 Sales Overview -->
    <VCol
      cols="12"
      md="3"
      sm="6"
    >
      <AnalyticsSalesOverview />
    </VCol>

    <!-- 👉 Statistics Vertical -->
    <VCol
      cols="12"
      md="3"
      sm="6"
    >
      <CardStatisticsVertical v-bind="statisticsVertical" />
    </VCol>

    <!-- 👉 Earning Reports Weekly Overview -->
    <VCol
      cols="12"
      md="6"
    >
      <AnalyticsIncomingVacationTable  :vacation="incomingVacationList"/>

      <!--      <AnalyticsEarningReportsWeeklyOverview />-->
    </VCol>

    <!-- 👉 Support Tracker -->
    <VCol
      cols="12"
      md="6"
    >
      <!-- -->
      <AnalyticsVacationTable :vacation="vacationList"/>


    </VCol>

    <!-- 👉 Sales by Countries -->
    <VCol
      cols="12"
      sm="6"
      lg="4"
    >
      <AnalyticsSalesByCountries />
    </VCol>

    <!-- 👉 Total Earning -->
    <VCol
      cols="12"
      sm="6"
      lg="4"
    >
      <AnalyticsTotalEarning />
    </VCol>

    <!-- 👉 Monthly Campaign State -->
    <VCol
      cols="12"
      sm="6"
      lg="4"
    >
      <AnalyticsSupportTracker />

    </VCol>

    <!-- 👉 Source Visits -->
    <VCol
      cols="12"
      sm="6"
      lg="4"
    >
      <AnalyticsSourceVisits />
    </VCol>

    <!-- 👉 Project Table -->
    <VCol
      cols="12"
      lg="8"
    >
      <AnalyticsMonthlyCampaignState />

    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/libs/apex-chart.scss";
</style>

<route lang="yaml">
meta:
  action: admin
  subject: dashboard
</route>
