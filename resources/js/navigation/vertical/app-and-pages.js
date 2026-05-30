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
    title: 'Import Clients', to: 'apps-client-import', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.CLIENT.ADD,
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
    title: 'Import Employees', to: 'apps-employee-import', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.EMPLOYEE.ADD,
    subject:PERMISSIONS.EMPLOYEE.SUBJECT
  },
  {
    title: 'Import Careers', to: 'apps-employee-import-careers', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.EMPLOYEE.ADD,
    subject:PERMISSIONS.EMPLOYEE.SUBJECT
  },
  {
    title: 'Import Vacations', to: 'apps-employee-import-vacations', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.VACATION.ADD,
    subject:PERMISSIONS.VACATION.SUBJECT
  },
  {
    title: 'Product', to: 'apps-product-list', icon: { icon: 'tabler-box' },
    action:PERMISSIONS.PRODUCT.LIST,
    subject:PERMISSIONS.PRODUCT.SUBJECT
  },
  {
    title: 'Import Products', to: 'apps-product-import', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.PRODUCT.ADD,
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
      { title: 'Import Invoices', to: 'apps-certifyInvoice-import',
        action:PERMISSIONS.CERTIFY_INVOICE.ADD,
        subject:PERMISSIONS.CERTIFY_INVOICE.SUBJECT},
      { title: 'Import Invoice Products', to: 'apps-certifyInvoice-import-products',
        action:PERMISSIONS.CERTIFY_INVOICE.ADD,
        subject:PERMISSIONS.CERTIFY_INVOICE.SUBJECT},
      { title: 'Import Clients', to: 'apps-certifyClient-import',
        action:PERMISSIONS.CERTIFY_INVOICE.ADD,
        subject:PERMISSIONS.CERTIFY_INVOICE.SUBJECT},
      { title: 'Import Products', to: 'apps-certifyProduct-import',
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
      { title: 'Import', to: 'apps-POS-sale-import',
        action:PERMISSIONS.SALE.ADD,
        subject:PERMISSIONS.SALE.SUBJECT
      },
      { title: 'Import Items', to: 'apps-POS-sale-import-items',
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
      { title: 'Import Returns', to: 'apps-POS-return-import',
        action:PERMISSIONS.RETURN.ADD,
        subject:PERMISSIONS.RETURN.SUBJECT
      },
      { title: 'Import Return Lists', to: 'apps-POS-return-import-lists',
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
    title: 'Import Payments', to: 'apps-POS-payment-import', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.PAYMENT.ADD,
    subject:PERMISSIONS.PAYMENT.SUBJECT
  },
  {
    title: 'Import Partial Payments', to: 'apps-POS-payment-import-partials', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.PAYMENT.ADD,
    subject:PERMISSIONS.PAYMENT.SUBJECT
  },
  {
    title: 'Import Cheques', to: 'apps-cheque-import', icon: { icon: 'tabler-file-upload' },
    action:PERMISSIONS.PAYMENT.ADD,
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
