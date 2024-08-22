import PERMISSIONS from "@/router/permissions";

export default [


  { heading: 'People' },
  {
    title: 'User', to: 'apps-user-list', icon: { icon: 'tabler-user' },
    action:PERMISSIONS.USER.LIST,
    subject:PERMISSIONS.USER.SUBJECT
  },
  {
    title: 'Client', to: 'apps-client-list', icon: { icon: 'tabler-address-book' },
    action:PERMISSIONS.CLIENT.LIST,
    subject:PERMISSIONS.CLIENT.SUBJECT
  },
  {
    title: 'Supplier', to: 'apps-supplier-list', icon: { icon: 'tabler-briefcase' },
    action:PERMISSIONS.SUPPLIER.LIST,
    subject:PERMISSIONS.SUPPLIER.SUBJECT
  },
  {
    title: 'Employee', to: 'apps-employee-list', icon: { icon: 'tabler-user' },
    action:PERMISSIONS.EMPLOYEE.LIST,
    subject:PERMISSIONS.EMPLOYEE.SUBJECT

  },
  {
    title: 'Product', to: 'apps-product-list', icon: { icon: 'tabler-box' },
    action:PERMISSIONS.PRODUCT.LIST,
    subject:PERMISSIONS.PRODUCT.SUBJECT
  },
  {
    title: 'Certify Invoices',
    children: [
      { title: 'List', to: 'apps-certifyInvoice-list',
        action:PERMISSIONS.CERTIFY_INVOICE.LIST,
        subject:PERMISSIONS.CERTIFY_INVOICE.SUBJECT},
      { title: 'Add', to: 'apps-certifyInvoice-add' ,
        action:PERMISSIONS.CERTIFY_INVOICE.ADD,
        subject:PERMISSIONS.CERTIFY_INVOICE.SUBJECT},
    ],
    icon: { icon: 'tabler-archive' },
  },
  { heading: 'POS' },
  {
    title: 'Invoice',
    children: [
      { title: 'List', to: 'apps-POS-sale-list',
        action:PERMISSIONS.SALE.LIST,
        subject:PERMISSIONS.SALE.SUBJECT
      },
      { title: 'Add', to: 'apps-POS-sale-add',
        action:PERMISSIONS.SALE.ADD,
        subject:PERMISSIONS.SALE.SUBJECT
      },

    ],
    icon: { icon: 'tabler-archive' },
  },
  {
    title: 'Return',
    children: [
      { title: 'List', to: 'apps-POS-return-list',
        action:PERMISSIONS.RETURN.LIST,
        subject:PERMISSIONS.RETURN.SUBJECT
      },
      { title: 'Add', to: 'apps-POS-return-add',
        action:PERMISSIONS.RETURN.ADD,
        subject:PERMISSIONS.RETURN.SUBJECT
      },

    ],
    icon: { icon: 'tabler-archive' },
  },
  {
    title: 'Payments', to: 'apps-POS-payment-list', icon: { icon: 'tabler-currency-dollar' },
    action:PERMISSIONS.PAYMENT.LIST,
    subject:PERMISSIONS.PAYMENT.SUBJECT
  },
  {
    title: 'Benefits', to: 'apps-POS-benefit-list', icon: { icon: 'tabler-help' },

    action:PERMISSIONS.BENEFIT.LIST,
    subject:PERMISSIONS.BENEFIT.SUBJECT
  },
  {
    title: 'Benefits Ultra', to: 'apps-POS-benefitUltra-list', icon: { icon: 'tabler-help' },
    action:PERMISSIONS.BENEFIT.LIST,
    subject:PERMISSIONS.BENEFIT.SUBJECT
  },
  {
    title: 'Management',
    children: [
      { title: 'Vacation', to: 'apps-employee-vacation-list',
        action:PERMISSIONS.VACATION.LIST,
        subject:PERMISSIONS.VACATION.SUBJECT
      },

      { title: 'Attendance', to: 'apps-attendance-list',
        action:PERMISSIONS.ATTENDANCE.LIST,
        subject:PERMISSIONS.ATTENDANCE.SUBJECT
      },
    ],
    icon: { icon: 'tabler-archive' },
  },
  {
    title: 'Roles & Permissions',
    children: [
      {
        title: 'Role',
        to: 'apps-role-list',
        action:PERMISSIONS.ROLE.LIST,
        subject:PERMISSIONS.ROLE.SUBJECT
      },
      {
        title: 'Permissions',
        to: 'apps-permission-list',
        action:PERMISSIONS.PERMISSION.LIST,
        subject:PERMISSIONS.PERMISSION.SUBJECT
      },
    ],
    icon: { icon: 'tabler-archive' },

  },

  {
    title: 'Maintenance & Assets',
    children: [
      {
        title: 'Assets',
        to: 'apps-asset-list',
        action:PERMISSIONS.ASSETS.LIST,
        subject:PERMISSIONS.ASSETS.SUBJECT
      },
      {
        title: 'Components',
        to: 'apps-component-list',
        action:PERMISSIONS.COMPONENT.LIST,
        subject:PERMISSIONS.COMPONENT.SUBJECT
      },
      {
        title: 'Maintenance',
        to: 'apps-maintenance-list',
        action:PERMISSIONS.MAINTENANCE.LIST,
        subject:PERMISSIONS.MAINTENANCE.SUBJECT
      },

    ],
    icon: { icon: 'tabler-archive' },

  },

]
