<script setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

// Components
import InvoiceAddPaymentDrawer from '@/views/apps/invoice/InvoiceAddPaymentDrawer.vue'
import InvoiceSendInvoiceDrawer from '@/views/apps/invoice/InvoiceSendInvoiceDrawer.vue'
import {useEmployeeStore} from "@/views/apps/employee/useEmployeeStore";

// Store
const route = useRoute()

const props = defineProps({
  employee: {
    type: Object,
  },
  name:{
    type:String
  }
})
const loading = ref({
  isActive: false
})
const employeeStore = useEmployeeStore()
const employee = ref({
   id: 1,
  name: "",
  surname: "",
  birthdate: "",
  birthplace: "",
  email: "",
  address: "",
  phone: "",
  NIN: "",
  NCN: "",
  CNAS: "",
  card_issue_date: "",
  card_issue_place: "",
  active: "",
  created_at: "",
  updated_at: "",
  card_issued_city:{
    name:'',
    name_ar:''
  },
  birth_city:{
    name:'',
    name_ar:''
  }
})
const fetchEmployee = (id) => {
  loading.isActive = true;
  employeeStore.fetchEmployee(id).then(response => {
    loading.isActive = false;
    employee.value = response.data.employee
    console.log(employee)
  }).catch(error => {

    loading.isActive = false;
    console.error(error)

  })
}
watchEffect(fetchEmployee(route.params.id))
// 👉 Print Invoice
const printInvoice = () => {
  window.print()
}
</script>

<template>
  <section >
    <v-overlay
      :model-value="loading.isActive"
      class="align-center justify-center"
    >
      <v-progress-circular
        color="primary"
        indeterminate
        size="64"
      ></v-progress-circular>
    </v-overlay>
    <VRow>
      <VCol
        class="printed"
        cols="12"
        md="9"
      >
        <VCard >
          <VCardText  class=" print-row pb-0 pt-0">
            <div>
              <div ref="html2Pdf" class="text-right" dir="rtl">
                <div class=" text-center">
                  <h6 class="header-font-size">
                   <u>EURL SETIFIS DETERGENTS</u>

                  </h6>
                  <h4 class="text-left header-font-size">
                    <u>Adresse</u>: LOTISSEMENT 34 SECT 6 GROUPE N° 51 KASR EL ABTAL

                  </h4>
                  <h6 class="text-left header-font-size">
                    <u> Activité</u> : FABRICATION DE PRODUITS DE BLANCHISEMENTS ET PRODUITS DE LA MAINT


                  </h6>
                  <h6 class="text-left header-font-size" >
                    <u>Numéro employeur</u> : 1960328646 M.F. : 001319010024074

                  </h6>
                  <VDivider class="mt-5 mb-5"/>
                </div>
                <h6 class="text-right">رقم : .................</h6>
                <h1 class="text-center mb-5 high-size">*** عقد عمل محدد المدة ***</h1>

                <h6>بناء على القانون 11/90 المؤرخ في 21 أفريل 1990 والمتعلق بعلاقات العمل المعدل والمتمم.</h6>

                <h6 class="text-center underline"><u>تم الاتفاق بين:</u></h6>
                <h6 class="text-right">
                   شركة  <b>EURL SETIFIS DETERGENTS</b> الكائن مقرها قطعة رقم 34 تجزئة 06 مجموعة رقم 51 قصر الابطال، عين ولمان، سطيف
                </h6 >
                <h6 class="text-right"><u>من جهة</u></h6>

                <h6 class="text-right " dir="rtl">والسيــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــد :
                  {{ employee.name_ar }} {{employee.surname_ar}}،
                  المولود (ة) في :{{employee.birthdate}} ب :{{ employee.birthplace }}، {{employee.birth_city.name_ar}}
                  حامل لبطاقة التعريف الوطنية رقم :{{employee.NCN}} ورقم التعريف الوطني : {{employee.NIN}} المسلمة بتاريخ:{{employee.card_issue_date}}

                </h6>
                <h6 class="text-right"><u>من جهة أخرى</u></h6>
                <h6 class="text-right"><u>و تم الاتفاق على ما يلي:</u></h6>
                <h4> <u>المادة 01:</u> <span>تشغل EURL SETIFIS DETERGENTS السيد:   {{ employee.name_ar }} {{employee.surname_ar}} بصفته: ...................</span></h4>

                <h2><u>المادة 02:</u><span>يتقاضى على هذا الأساس مبلغا قاعديا:................... دج</span></h2>

                <h2><u>المادة 03:</u><span>يستفيد المعني من مزايا الضمان الاجتماعي، العطل والخدمات الاجتماعية.</span></h2>

                <h2><u>المادة 04:</u> <span>يسرى هذا العقد من: ................ إلى ................</span></h2>

                <h2><u>المادة 05:</u> <span>يخضع المعني لفترة تجربة مدتها ..........................</span></h2>

                <h2><u>المادة 06:</u>                <span>خلال فترة التجربة وفترة تمديد التجربة يمكن قطع علاقة العمل في أي وقت من أحد الطرفين دون إشعار مسبق ودون تعويضات.</span>
                </h2>
                <h2><u>المادة 07:</u>                <span>إذا كانت فترة التجربة غير مرضية إما أن تمدد فترة التجريب إلى مدة متساوية أو تقطع علاقة العمل.</span>
                </h2>
                <h2><u>المادة 08:</u>                <span>تنتهي علاقة العمل بانتهاء المدة المتعاقد عليها.</span>
                </h2>
                <h2><u>المادة 09:</u>                <span>يمكن تجديد هذا العقد إذا تطلب ذلك ضرورة للمصلحة وبنفس الشكل لمدة يحددها الطرفان.</span>
                </h2>
                <h2><u>المادة 10:</u>                <span>يستفيد العامل بجميع الحقوق ويلزم بجميع الواجبات المحددة في التشريع والتنظيم المعمول بهما.</span>
                </h2>
                <h2><u>المادة 11:</u>                <span>الأمور غير الواضحة في هذا العقد يرجع فيها إلى النظام الداخلي الساري المفعول المطلع عليه من طرف العامل، كما يلزم احترام العقد بدقة.</span>
                </h2>
                <h2><u>المادة 12:</u> <span>                  يتوجب على الموظفين إتمام فترة انتقالية بعد تقديمهم الاستقالتهم تحدد مدتها الشركة ، وذلك لضمان استكمال المهام و تسليم المسؤوليات بشكل منظم و سلس.
</span></h2>

                <h2><u>المادة 13:</u> <span>مكان العمل: عامل داخل مقر الشركة.</span></h2>
                <VRow>
                  <VCol cols="6">
                    <h6>
                      توقيع المعني: .......................................
                  </h6>
                    <h6>مع كتابة الإسم واللقب</h6>
                  </VCol>
                  <VCol cols="6">
                    <h6>

                      توقيع الرئيس المدير العام: .......................................
                    </h6>
                  </VCol>
                </VRow>


              </div>
            </div>
          </VCardText>

        </VCard>


        <div class="page-break">
          <VCard>
            <VCardText  class=" print-row pb-0">
              <div>
                <div ref="html2Pdf" class="text-right" dir="rtl">
                  <div ref="html2Pdf" class="text-right" dir="rtl">

                    <h1 class="text-center high-size">الجمهورية الجزائرية الديمقراطية الشعبية</h1>
                    <div class="mt-10 mb-10">
                      <h2>شركــــــة: EURL SETIFIS DETERGENTS</h2>
                      <h2>عنوان الشركة : قطعة 34 قسم 6 مجموعة رقم 51 قصر الابطال سطيف الجزائر</h2>
                    </div>


                    <h2 class="text-center text-decoration-underline  medium-size font-weight-bold mb-10" >محضر إعتراف بتقاضي الحقوق والمستحقات</h2>
                    <ul>
                      <li> <h2>المرجع:</h2></li>
                      <li> <h2>رقم العقد: ……………………….. المؤرخ في: ………………………</h2></li>
                      <li><h2>طبقا للمادة 10 من قانون العمل.</h2></li>
                      <li><h2>يصرح المسمى  {{ employee.name_ar }} {{employee.surname_ar}} ،المولود بتاريخ: {{employee.birthdate}} ، بـ:{{employee.birthplace}}، {{employee.birth_city.name_ar}}.</h2></li>
                      <li><h2>إبن:{{employee.father_name_ar }}.  و: {{ employee.mother_full_name_ar }}.</h2></li>
                      <li><h2>الحامل لبطاقة التعريف الوطنية رقم:{{employee.NCN}}. و رقم التعريف الوطني : {{employee.NIN}} الصادرة بتاريخ: {{employee.card_issue_date}}، عن {{employee.card_issue_place}} الولاية: {{employee.card_issued_city.name_ar}} .</h2></li>
                      <li><h2>أنني إستلمت جميع حقوقي وأمضيته بمحضر إرادتي ودون إكراه من أحد.</h2></li>
                      <li class="text-left"><h2>حرر بسطيف يوم: ………………………. </h2></li>
                      <li><h2>إمضاء وبصمة المعني</h2></li>
                    </ul>



                  </div>

                </div>
              </div>
            </VCardText>

          </VCard>
        </div>
        <div class="page-break">
          <VCard>
            <VCardText  class=" print-row pb-0">
              <div>
                <div ref="html2Pdf" >
                  <div ref="html2Pdf" >
                    <div class="text-right text-center">
                      <p><strong>EURL SETIFIS DETERGENT</strong><br>
                        <strong>Adresse : LOTISSEMENT 34 SECT 6 GROUPE N° 51 KASR EL ABTAL</strong><br>
                        <strong> Activité : FABRICATION DE PRODUITS DE BLANCHISEMENTS ET PRODUITS DE LA MAINT</strong>
                        <br>
                      </p>
                    </div>
                    <VDivider/>
                    <p class="mt-10">
                      <strong>Nom : {{employee.surname}} </strong><br>
                      <strong>Prénom : {{employee.name}} </strong><br>
                      <strong>Numéro carte nationale : {{employee.NCN}} </strong><br>
                      <strong>Numéro d'identification nationale : {{employee.NIN}} </strong><br>
                      <br>
                    </p>







                    <p><strong>Objet :</strong> Démission</p>

                    <p>Madame, Monsieur,</p>

                    <p>Par la présente, je, {{employee.surname}} {{employee.name}}, titulaire de la carte nationale d'identité numéro {{employee.NCN}} et d'identification nationale numéro {{employee.NIN}}, informe de ma décision de démissionner de mon poste au sein de <strong>EURL SETIFIS DETERGENTS</strong>. Ma démission sera effective dans a partir de <strong>............................</strong>.</p>

                    <p class="text-right"><strong>[Signature]</strong></p>


                  </div>

                </div>
              </div>
            </VCardText>

          </VCard>
        </div>

        <div class="page-break">
          <VCard>
            <VCardText  class=" print-row pb-0">
              <div>
                <div ref="html2Pdf" >
                  <div ref="html2Pdf" dir="rtl">
                    <div class="text-right text-center">
                      <p><strong>EURL SETIFIS DETERGENTS</strong><br>
                        <strong>العنوان: قطعة 34 قسم 6 مجموعة رقم 51 قصر الأبطال</strong><br>
                        <strong>النشاط: تصنيع منتجات التبييض ومنتجات الصيانة</strong><br>
                      </p>
                    </div>
                    <VDivider/>
                    <p class="mt-10">
                      <strong>الاسم:{{employee.surname_ar}} </strong><br>
                      <strong>اللقب: {{ employee.name_ar }} </strong><br>
                      <strong>رقم البطاقة الوطنية: {{ employee.NCN }} </strong><br>
                      <strong>رقم التعريف الوطني: {{ employee.NIN }} </strong><br>
                      <br>
                    </p>

                    <p><strong>الموضوع :</strong> الاستقالة</p>

                    <p> سيدي المدير ،</p>

                    <p> أنا الموظف :{{employee.surname_ar}} {{ employee.name_ar }}، حامل لبطاقة الهوية الوطنية رقم {{employee.NCN}} ورقم التعريف الوطني
                      {{ employee.NIN }}، أعلمكم بقراري بالاستقالة من منصبي في شركة <strong>EURL SETIFIS DETERGENTS</strong>.   اعتباراً من <strong>............................</strong>.</p>

                    <p class="text-left"><strong>سطيف في :..............</strong></p>
                    <p class="text-right"><strong>التوقيع</strong></p>


                  </div>

                </div>
              </div>
            </VCardText>

          </VCard>
        </div>
      </VCol>

      <VCol
        cols="12"
        md="3"
        class="d-print-none"
      >
        <VCard>
          <VCardText>
            <!-- 👉 Send Invoice Trigger button -->
            <VBtn
              block
              prepend-icon="tabler-send"
              class="mb-2"
              @click="printInvoice"
            >
              Print
            </VBtn>



          </VCardText>
        </VCard>
        <!-- 👉 Payment Terms -->




      </VCol>
    </VRow>

    <!-- 👉 Add Payment Sidebar -->

    <!-- 👉 Send Invoice Sidebar -->
  </section>
</template>

<style lang="scss">
li{
  padding:7px
}
*{
  font-size: 16px;
}
.header-font-size{
  font-size: 15px !important;
}
.medium-size{
  font-size: 25px !important;
}
.high-size{
  font-size: 37px;
}
.page-break {
  page-break-before: always; /* or page-break-after: always; */
}
@media print {


  * {
    border-color: rgb(0 0 0 / 30%) !important;
  }
  * {
    color: black !important;
  }
  .layout-vertical-nav{
    display: none;
  }

  .v-application {
    background: none !important;
  }

  @page { margin: 0; size: auto; }

  .layout-page-content,
  .v-row,
  .v-col-md-9 {
    padding: 0;
    margin: 0;
  }

  .product-buy-now {
    display: none;
  }

  .v-navigation-drawer,
  .layout-vertical-nav,
  .app-customizer-toggler,
  .layout-footer,
  .layout-navbar,
  .layout-navbar-and-nav-container {
    display: none;
  }

  .v-col-md-6 {
    flex: 0 0 50% !important;
    max-width: 50% !important;
  }
  .v-col-md-10 {
    flex: 0 0 83.3333333333%;
    max-width: 83.3333333333%;
  }

  .v-col-md-2 {
    flex: 0 0 16.6666666667%;
    max-width: 16.6666666667%;
  }
  .v-col-md-8 {
    flex: 0 0 66.6666666667%;
    max-width: 66.6666666667%;
  }
  .v-col-md-7 {
    flex: 0 0 58.3333333333% !important;
    max-width: 58.3333333333% !important;
  }
  .v-col-md-5 {
    flex: 0 0 41.6666666667% !important;
    max-width: 41.6666666667% !important;
  }
  .v-col-md-4 {
    flex: 0 0 33.3333333333%;
    max-width: 33.3333333333%;
  }
  .v-col-md-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }
  .v-col-md-2 {
    flex: 0 0 16.6666666667%;
    max-width: 16.6666666667%;
  }

  .v-card {
    box-shadow: none !important;

    .print-row {
      flex-direction: row !important;
    }
  }

  .layout-content-wrapper {
    padding-inline-start: 0 !important;
  }
  .text-color-black{
    color: black !important;
  }

  .invoiceGeneral{
    background-color: white !important;
    border-radius: 10px !important;
    padding: 0px !important;
  }
  .display-center{
    display: inline-grid
  }

  .custom-border{
    padding: 10px 0 10px 15px;
    background-color: #f3f2f4;
    border-radius: 19px;
    margin-right: 6px;
    margin-bottom: 6px;
  }
  .custom-white-border{
    height: 30px;
    background-color: white;
    border-radius: 19px;
    margin-right: 12px;
    border: 1px solid #e4e4e4;
    margin-bottom: 6px;
  }
  .border-right{
    font-size: 10px !important;
    height: 22px;
    padding: 0 8px 0;
    border-right: solid 1px #dbdbdb ;
  }
  .text-sm-caption{
    font-size: 11px !important;
    font-weight: bold;
  }
  .text-sm-subtitle-2{
    font-weight: bold;
  }

  .padding-8{
    padding: 1px 8px 0;
  }
  .padding-right-0{
    padding-right: 0;
  }
  .text-sm-subtitle-2 {
    font-size: 0.875rem !important;
    font-weight: 500;
    line-height: 1.375rem;
    letter-spacing: 0.0071428571em !important;
    font-family: "Public Sans", sans-serif, -apple-system, blinkmacsystemfont, "Segoe UI", roboto, "Helvetica Neue", arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol" !important;
    text-transform: none !important;
  }
  .data-font{
    font-size: 12px !important;
    font-weight: bold;
  }
  .border-left{

  }
}
.text-color-black{
  color: black !important;
}
.invoiceGeneral{
  background-color: white !important;
  border-radius: 10px !important;
  padding: 0 !important;

}
.display-center{
  display: inline-grid
}

.custom-border{
  padding: 10px 0 10px 15px;
  background-color: #f3f2f4;
  border-radius: 19px;
  margin-right: 6px;
  margin-bottom: 6px;
}
.custom-white-border{
  height: 23px;
  background-color: white;
  border-radius: 19px;
  margin-right: 12px;
  border: 1px solid #e4e4e4;
  margin-bottom: 6px;
}

.border-right{
  font-size: 10px !important;
  height: 22px;
  padding: 0 8px 0;
  border-right: solid 1px #dbdbdb;
}
.border-left{

}
.text-success{
  color:green;
  font-weight:bold;
}
.padding-8{
  padding: 1px 8px 0;
}
.padding-right-0{
  padding-right: 0;
}

.data-font{
  font-size: 10px !important;
  font-weight: bold;
}
.v-autocomplete__selection-text{
  font-size: small;
}
table > tbody > tr > td, table > thead > tr > th{
  height: 23px !important;
}
</style>
<route lang="yaml">
meta:
  action: list
  subject: employees
</route>
