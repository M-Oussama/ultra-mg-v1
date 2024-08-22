import PERMISSIONS from "@/router/permissions";

export default [
  {
    title: 'Dashboards',
    icon: { icon: 'tabler-smart-home' },
    children: [
      {
        title: 'Analytics',
        to: 'dashboards-analytics',
        action:PERMISSIONS.ADMIN.NAME,
        subject:PERMISSIONS.ADMIN.DASHBOARD
      },
      {
        title: 'eCommerce',
        to: 'dashboards-ecommerce',
        action:PERMISSIONS.ADMIN.NAME,
        subject:PERMISSIONS.ADMIN.DASHBOARD
      },
      {
        title: 'CRM',
        to: 'dashboards-crm',
        action:PERMISSIONS.ADMIN.NAME,
        subject:PERMISSIONS.ADMIN.DASHBOARD
      },
      {
        title: 'Dashboard',
        to: 'dashboards-maintenance',
        action:PERMISSIONS.MAINTENANCE.NAME,
        subject:PERMISSIONS.MAINTENANCE.DASHBOARD
      },
    ],
    badgeContent: '2',
    badgeClass: 'bg-light-primary text-primary',
  },
]
