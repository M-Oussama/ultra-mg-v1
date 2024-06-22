<template>
  <p class="text-body2">{{ balance }}</p>
  <div class="container">
    <div class="list">
<!--      <input v-model="searchLeft" placeholder="Search" />-->
      <div v-for="item in props.left" :key="item.id" @click="moveItemToRight(item, props.left, props.right)">
        {{ parseFloat(item.balance).toFixed(2) }}
      </div>
    </div>

    <div class="controls">
      <div @click="moveAll()">&gt;&gt;</div>

      <div @click="undoAll()">&lt;&lt;</div>
    </div>

    <div class="list">
<!--      <input v-model="searchRight" placeholder="Search" />-->
      <div v-for="item in props.right" :key="item.id" @click="moveItemToLeft(item, props.right, props.left)">
        {{ parseFloat(item.amount).toFixed(2) }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineEmits, defineProps, reactive, ref } from 'vue'

const props = defineProps({
  balance: { type: String, required: false },

  amount:{
    type:Object
  },
  left:{
    type:Object
  },
  leftTemp:{
    type:Object
  },
  right:{
    type:Object
  }
})
const remainingAmount = ref(0)
let left = reactive([])
const right = reactive([])
let filteredLeft = computed(() => left.filter((item) => item?.total_amount.toLowerCase().includes(searchLeft.value.toLowerCase())))
let filteredRight = computed(() => right.filter((item) => item?.total_amount.toLowerCase().includes(searchRight.value.toLowerCase())))

const callFilter =() =>{
  filteredLeft = computed(() => left.filter((item) => item?.total_amount.toLowerCase().includes(searchLeft.value.toLowerCase())))
  filteredRight = computed(() => right.filter((item) => item?.total_amount.toLowerCase().includes(searchRight.value.toLowerCase())))
}
const firstFilter = ref(false)

watchEffect(() => {
    remainingAmount.value = parseFloat(props.amount)

    callFilter()
})
watch(() => props.amount, () => {
  undoAll()
});

const searchLeft = ref('')
const searchRight = ref('')

const moveItemToRight = (item, from, to) => {
  recalculateBoxLTR(item , from, to)
}
const moveItemToLeft = (item, from, to) => {
  recalculateBoxRTL(item , from, to)
}
const recalculateBoxLTR = (invoice, from , to) => {
  console.log(props)
  if(invoice.amount === undefined){
    invoice.amount = invoice.balance;
  }
  const invoiceAmount = parseFloat(invoice.balance);
  if (remainingAmount.value >= invoiceAmount) {
     remainingAmount.value = parseFloat(remainingAmount.value) - invoiceAmount;
     to.push(invoice);

    const index = from.indexOf(invoice)
    from.splice(index, 1)

  } else if (remainingAmount.value > 0) {
    // Create a partial invoice with the remaining amount
    const partialInvoice = { ...invoice };

    if(partialInvoice.sale_id === undefined)
      partialInvoice.sale_id = partialInvoice.id

    partialInvoice.balance = remainingAmount.value
    partialInvoice.amount = remainingAmount.value

    to.push(partialInvoice);
    invoice.balance = parseFloat(invoice.balance ) - remainingAmount.value;
    invoice.amount = parseFloat(invoice.balance ) - remainingAmount.value;

    remainingAmount.value = 0;
  }

}

const recalculateBoxRTL = (invoice, from , to) => {
  if(invoice.amount !== undefined)
    invoice.balance = parseFloat(invoice.amount);

  const invoiceAmount = parseFloat(invoice.balance);

  let leftInvoice = ref()
  if(invoice.sale_id !== undefined)
     leftInvoice = findInvoice(invoice.sale_id);
  else
    leftInvoice = findInvoice(invoice.id);

  remainingAmount.value += invoiceAmount;
  if(leftInvoice !== undefined) {
    leftInvoice.balance = parseFloat(leftInvoice.balance) + invoiceAmount
    leftInvoice.amount = parseFloat(leftInvoice.balance) + invoiceAmount

    const index = from.indexOf(invoice)
    from.splice(index, 1)


  }else {
    const index = from.indexOf(invoice)
    from.splice(index, 1)
    to.push(invoice)
  }

}

const findInvoice = (id) => {

  for (let i = 0; i <props.left.length ; i++) {
    if(props.left[i].id === id)
      return props.left[i];
  }
}
const undoAll = () =>{
  while(props.right.length > 0) {
    props.right.map(function(value, key) {
      recalculateBoxRTL(value, props.right , props.left)

    });
  }
}
const goback = () =>{
  console.log(props.leftTemp)
  while(props.left.length > 0) {props.left.pop();}

  for (let i = 0; i <props.leftTemp.length ; i++) {
    props.left.push({...props.leftTemp[i]})
  }
  while(props.right.length > 0) {props.right.pop();}

}
const moveAll = () => {
  while(remainingAmount.value > 0 && props.left.length > 0) {

    props.left.map(function(value, key) {
      recalculateBoxLTR(value, props.left, props.right)

    });

  }
}

const swap = () => {
  const removed = left.splice(0, left.length, ...right)
  right.splice(0, right.length, ...removed)
}
</script>

<style lang="scss">
body {
  background-color: #1c1c1c;
}

.container {
  display: flex;
}

.list {
  border: 1px solid #cacacd;
  color: color-scheme;
  height: 300px;
  overflow-x: hidden;
  overflow-y: auto;
  width: 100%;

  & > input {
    background: transparent;
    border: none;
    border-bottom: 1px solid hsla(0, 0%, 100%, 0.6);
    color: hsla(0, 0%, 100%, 0.6);
    font-size: 1em;
    outline: none;
    padding: 5px;
    text-align: center;
    width: 100%;
  }

  & > div {
    border-bottom: 1px solid #cacacd;
    font-size: 0.9em;
    padding: 5px;

    text-align: center;
    color: black;
    &:hover {
      background-color: hsla(0, 0%, 100%, 0.07);
      cursor: pointer;
    }
  }
}

.controls {
  display: flex;
  flex: none;
  flex-direction: column;
  gap: 10px 10px;
  margin: auto 10px;

  & > div {
    border: none;
    color: grey;
    font-size: 1rem;
    cursor: pointer;
    &:hover {
      cursor: pointer;
      color: #000;
    }
  }
}
.list::-webkit-scrollbar {
  width: 10px;
}
.list::-webkit-scrollbar-track {
  background-color: darkgrey;
}
.list::-webkit-scrollbar-thumb {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
}
.list {
  scrollbar-width: thin;
}

</style>
