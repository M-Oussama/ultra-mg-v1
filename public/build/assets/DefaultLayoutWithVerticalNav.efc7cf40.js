import{t as m,i as d,o as a,b as n,w as e,q as o,bb as h,r as b,m as f,p as t,C as u,y as g,T as v,e as _}from"./main.eb2abb0f.js";import{_ as y}from"./TheCustomizer.b693adb2.js";import C from"./Footer.33f61aba.js";import x from"./NavBarI18n.1ebfa36f.js";import S from"./NavBarNotifications.42390291.js";import k from"./NavbarShortcuts.a3c18c79.js";import P from"./NavbarThemeSwitcher.1d6a235c.js";import A from"./NavSearchBar.5484d4ea.js";import T from"./UserProfile.d613ed7c.js";import{V}from"./VBtn.4b29655b.js";import{V as L}from"./VSpacer.47fd3527.js";import"./VDivider.d741eb3a.js";import"./index.1d60d957.js";import"./VRadioGroup.02f6f664.js";import"./VSelectionControl.b7f754bc.js";import"./router.cee8299d.js";import"./VInput.12678d62.js";import"./VImg.c89b2431.js";import"./VSwitch.10a5493c.js";import"./position.4305ca24.js";import"./VRow.fc64865f.js";import"./VSelect.1a2459fe.js";import"./VTextField.cfaf068e.js";/* empty css                   */import"./VField.d31c7729.js";import"./easing.36b781ab.js";import"./forwardRefs.c003b6b8.js";import"./VCounter.28fd36d2.js";import"./VList.abe45db6.js";import"./VAvatar.ef806db0.js";import"./dialog-transition.ae223f33.js";import"./VMenu.6af9ca11.js";import"./scopeId.deee8bf4.js";import"./VOverlay.8b37277a.js";import"./lazy.4714770b.js";import"./VCheckboxBtn.c57877f7.js";import"./VChip.574f5191.js";import"./VNavigationDrawer.5a003ebe.js";import"./ssrBoot.b678eb07.js";import"./formatters.4acf44a4.js";import"./index.b522f886.js";import"./VBadge.82cc280a.js";import"./VListItemAction.65d7d330.js";import"./VCard.d18cee11.js";import"./useAppAbility.4921c660.js";const w=[{heading:"People"},{title:"User",to:"apps-user-list",icon:{icon:"tabler-user"}},{title:"Client",to:"apps-client-list",icon:{icon:"tabler-address-book"}},{title:"Supplier",to:"apps-supplier-list",icon:{icon:"tabler-briefcase"}},{title:"Employee",to:"apps-employee-list",icon:{icon:"tabler-user"}},{title:"Product",to:"apps-product-list",icon:{icon:"tabler-box"}},{title:"Certify Invoices",children:[{title:"List",to:"apps-certifyInvoice-list"},{title:"Add",to:"apps-certifyInvoice-add"}],icon:{icon:"tabler-archive"}},{heading:"POS"},{title:"Invoice",children:[{title:"List",to:"apps-POS-sale-list"},{title:"Add",to:"apps-POS-sale-add"}],icon:{icon:"tabler-archive"}},{title:"Return",children:[{title:"List",to:"apps-POS-return-list"},{title:"Add",to:"apps-POS-return-add"}],icon:{icon:"tabler-archive"}},{title:"Payments",to:"apps-POS-payment-list",icon:{icon:"tabler-currency-dollar"}},{title:"Benefits",to:"apps-POS-benefit-list",icon:{icon:"tabler-help"}},{title:"Benefits Ultra",to:"apps-POS-benefitUltra-list",icon:{icon:"tabler-help"}},{title:"Management",children:[{title:"Vacation",to:"apps-employee-vacation-list"},{title:"Attendance",to:"apps-attendance-list"}],icon:{icon:"tabler-archive"}}],B=[{heading:"Charts"},{title:"Charts",icon:{icon:"tabler-chart-donut"},children:[{title:"Apex Chart",to:"charts-apex-chart"},{title:"Chartjs",to:"charts-chartjs"}]}],O=[{title:"Dashboards",icon:{icon:"tabler-smart-home"},children:[{title:"Analytics",to:"dashboards-analytics"},{title:"eCommerce",to:"dashboards-ecommerce"},{title:"CRM",to:"dashboards-crm"}],badgeContent:"2",badgeClass:"bg-light-primary text-primary"}],I=[{heading:"Forms"},{title:"Form Elements",icon:{icon:"tabler-copy"},children:[{title:"Checkbox",to:"forms-checkbox"},{title:"Combobox",to:"forms-combobox"},{title:"Date Time Picker",to:"forms-date-time-picker"},{title:"File Input",to:"forms-file-input"},{title:"Radio",to:"forms-radio"},{title:"Range Slider",to:"forms-range-slider"},{title:"Rating",to:"forms-rating"},{title:"Select",to:"forms-select"},{title:"Slider",to:"forms-slider"},{title:"Switch",to:"forms-switch"},{title:"Textarea",to:"forms-textarea"},{title:"Textfield",to:"forms-textfield"}]},{title:"Form Layouts",icon:{icon:"tabler-copy"},to:"forms-form-layouts"},{title:"Form Validation",icon:{icon:"tabler-checkbox"},to:"forms-form-validation"}],R=[{heading:"Others"},{title:"Access Control",icon:{icon:"tabler-shield"},to:"access-control",action:"read",subject:"Auth"},{title:"Nav Levels",icon:{icon:"tabler-menu-2"},children:[{title:"Level 2.1",to:null},{title:"Level 2.2",children:[{title:"Level 3.1",to:null},{title:"Level 3.2",to:null}]}]},{title:"Disabled Menu",to:null,icon:{icon:"tabler-eye-off"},disable:!0},{title:"Raise Support",href:"https://pixinvent.ticksy.com/",icon:{icon:"tabler-lifebuoy"},target:"_blank"},{title:"Documentation",href:"https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/",icon:{icon:"tabler-file"},target:"_blank"}],$=[{heading:"UI Elements"},{title:"Typography",icon:{icon:"tabler-square-letter-t"},to:"pages-typography"},{title:"Icons",icon:{icon:"tabler-eye"},to:"pages-icons"},{title:"Cards",icon:{icon:"tabler-credit-card"},children:[{title:"Basic",to:"pages-cards-card-basic"},{title:"Advance",to:"pages-cards-card-advance"},{title:"Statistics",to:"pages-cards-card-statistics"},{title:"Widgets",to:"pages-cards-card-widgets"},{title:"Actions",to:"pages-cards-card-actions"}]},{title:"Components",icon:{icon:"tabler-archive"},children:[{title:"Alert",to:"components-alert"},{title:"Avatar",to:"components-avatar"},{title:"Badge",to:"components-badge"},{title:"Button",to:"components-button"},{title:"Chip",to:"components-chip"},{title:"Dialog",to:"components-dialog"},{title:"Expansion Panel",to:"components-expansion-panel"},{title:"List",to:"components-list"},{title:"Menu",to:"components-menu"},{title:"Pagination",to:"components-pagination"},{title:"Progress",to:"components-progress"},{title:"Snackbar",to:"components-snackbar"},{title:"Tabs",to:"components-tabs"},{title:"Timeline",to:"components-timeline"},{title:"Tooltip",to:"components-tooltip"}]}],D=[...O,...w,...$,...I,...B,...R],N={class:"d-flex h-100 align-center"},It={__name:"DefaultLayoutWithVerticalNav",setup(F){const{appRouteTransition:r,isLessThanOverlayNavBreakpoint:s}=m(),{width:l}=d();return(E,j)=>{const c=b("RouterView"),p=y;return a(),n(o(h),{"nav-items":o(D)},{navbar:e(({toggleVerticalOverlayNavActive:i})=>[f("div",N,[o(s)(o(l))?(a(),n(V,{key:0,icon:"",variant:"text",color:"default",class:"ms-n3",size:"small",onClick:z=>i(!0)},{default:e(()=>[t(u,{icon:"tabler-menu-2",size:"24"})]),_:2},1032,["onClick"])):g("",!0),t(A,{class:"ms-lg-n3"}),t(L),t(x),t(P),t(k),t(S,{class:"me-2"}),t(T)])]),footer:e(()=>[t(C)]),default:e(()=>[t(c,null,{default:e(({Component:i})=>[t(v,{name:o(r),mode:"out-in"},{default:e(()=>[(a(),n(_(i)))]),_:2},1032,["name"])]),_:1}),t(p)]),_:1},8,["nav-items"])}}};export{It as default};