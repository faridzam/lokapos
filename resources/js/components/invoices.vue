<template>
  <div class="main">
    <div class="invoice-title-container">
        <h1 class="invoice-title-container-text">invoices</h1>
        <div class="flex-break"></div>
        <h4 class="invoice-title-container-amount">Deposit : Rp. {{deposit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h4>
        <h4 class="invoice-title-container-amount">Income : Rp. {{income.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h4>
        <h4 class="invoice-title-container-amount">Total : Rp. {{total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h4>
    </div>
    <div class="invoice-table-container">
        <table class="invoice-table">
            <tr class="invoice-table-header">
                <th style="width:5%">#</th>
                <th style="width:15%">Invoice Number</th>
                <th style="width:10%">Payment Method</th>
                <th style="width:15%">Bill Amount</th>
                <th style="width:15%">Pay Amount</th>
                <th style="width:10%">Change Amount</th>
                <th style="width:10%">Note</th>
                <th style="width:5%">Tax(%)</th>
                <th style="width:5%">Discount(%)</th>
                <th style="width:7%">Action</th>
            </tr>
            <tr v-for="(order, index) in orderToday" :key="order.id" class="invoice-table-item">
                <td style="width:5%">{{index+1}}</td>
                <td style="width:15%">{{order.no_invoice}}</td>
                <td style="width:10%">{{paymentMethodName(order.payment_id)}}</td>
                <td style="width:15%">Rp. {{order.bill_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</td>
                <td style="width:15%">Rp. {{order.pay_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</td>
                <td style="width:10%">Rp. {{order.change_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</td>
                <td style="width:10%">{{order.note}}</td>
                <td style="width:5%">{{order.tax}}%</td>
                <td style="width:5%">{{order.discount}}%</td>
                <td style="width:7%">
                    <button class="print-button cursor-pointer">
                        <span class="print-icon" v-on:click.prevent="printInvoice(order.id)">
                            <svg-vue icon="print-icon" style="width: 2rem; height: auto; fill: white;"></svg-vue>
                        </span>
                    </button>
                </td>
            </tr>
        </table>
    </div>
  </div>
</template>

<script>
export default {

    data() {
        return {
            //
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            orderToday: {},
            income: 0,
            deposit: 0,
            total: 0,
            paymentMethods:[],
        }
    },
    methods:{
        //
        getData(){
            this.income = 0;
            axios.get('api/getInvoices')
                .then((response)=>{
                this.orderToday = response.data.orderToday;
                this.income = response.data.incomeToday;
                this.deposit = response.data.depositToday;
                this.total = Number(this.income) + Number(this.deposit);
            }).catch((error) => {
                console.log(error);
            });
        },
        getPaymentMethods(){
            axios.get('/api/getPaymentMethods')
            .then((response)=>{
              this.paymentMethods = response.data.paymentMethods;
              //select default category
              //this.selectedMethods = response.data.paymentMethods[0].id;
            });
        },
        paymentMethodName(id){
            //
            var objs = this.paymentMethods;
            let obj = objs.find(paymentMethod => paymentMethod.id == id);

            return obj.name;
        },
        printInvoice(id){
            //

            const formData = new FormData();

            formData.append("_token", this.csrf);
            formData.append("id", id);
            axios.post("/printInvoice2", formData)
            .then(response => {
                console.log('ok');
                console.log(response.data);

                Vue.$toast.open({
                    message: 'invoice printed!',
                    type: 'success',
                    position: 'top',
                    duration: 2000,
                });
            })
            .catch(error => {
                //
                Vue.$toast.open({
                    message: 'invoice not printed!',
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });
                console.log(error);
            });

        }
    },
    mounted(){
        this.getData();
        this.getPaymentMethods();
    },
    computed: {
        //

    },

}
</script>

<style scoped>

    .flex-break{
        flex-basis: 100%;
        height: 0;
    }
    .main{
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        min-width: 95vw;
        min-height: 90vh;
        background-color: #F9F9F9;

        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px;
        border-radius: 10px;
    }
    .invoice-title-container{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin: 1rem 0 2rem 0;
    }
    .invoice-title-container-text{
        padding: 0;
        margin: 0;
    }
    .invoice-title-container-amount{
        padding: 0;
        margin: 1rem;
    }
    .invoice-table-container{
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
    }
    .invoice-table-item{
        padding: 1rem 0 0 0;
        text-align: center;
    }
    th {
        padding: 15px;
    }
    td{
        padding: 10px;
    }
    .print-button{
        padding: 0;
        margin: 0;
        height: 3rem;
        width: 5rem;
        outline: 0;
        border: 0px solid white;
        border-radius: 10px;
        background-color: #45ad8d;
        box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.1);
        transition: background 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;
    }
    .print-button:hover{
        background: #0ee0ac;
        box-shadow: 0 6px 20px rgb(0 118 255 / 23%);
    }

</style>
