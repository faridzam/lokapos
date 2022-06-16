<template>
  <div class="main">
    <div class="item-sales-header">
        <div class="item-sales-header-title">
            <h1>Item Sales</h1>
        </div>
    </div>
    <div class="item-sales-content">
        <table class="item-sales-table">
            <tr class="item-sales-table-header">
                <th>#</th>
                <th>Product Name</th>
                <th>Quantities</th>
                <th>Subtotal</th>
            </tr>
            <tr v-for="(order, index) in orderToday" :key="order.id" class="item-sales-table-content">
                <td>{{index+1}}</td>
                <td>{{order.name}}</td>
                <td>{{order.quantity}}</td>
                <td>Rp. {{order.total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</td>
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
        }
    },
    methods:{
        getData(){
            this.income = 0;
            axios.get('api/getItemSales')
                .then((response)=>{
                this.orderToday = response.data.orderToday;
                this.income = response.data.incomeToday;
                this.deposit = response.data.depositToday;
                this.total = Number(this.income) + Number(this.deposit);
            }).catch((error) => {
                console.log(error);
            });
        },
    },
    mounted(){
        this.getData();
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
    .item-sales-header{
        display: flex;
        justify-content: center;
    }
    .item-sales-header-title{}
    .item-sales-content{}
    .item-sales-table{
        width: 100%;
    }
    .item-sales-table-header{}
    .item-sales-table-content{
        text-align: center;
    }
    th {
        padding: 15px;
    }
    td{
        padding: 10px;
    }

</style>
