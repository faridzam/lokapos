<template>
    <div class="main row">
        <div class="left-wrapper col-8">
            <div class="saved-cart-container">
                <div class="saved-cart-title">
                    <h2>Saved Cart Lists</h2>
                </div>
                <div class="saved-cart-header">
                    <div class="saved-cart-header-text header-no">
                        <h3>#</h3>
                    </div>
                    <div class="saved-cart-header-text header-invoice">
                        <h3>Invoice Number</h3>
                    </div>
                    <div class="saved-cart-header-text header-bill">
                        <h3>Bill Amount</h3>
                    </div>
                    <div class="saved-cart-header-text header-notes">
                        <h3>Notes</h3>
                    </div>
                    <div class="saved-cart-header-text header-time">
                        <h3>Time</h3>
                    </div>
                </div>
                <ul class="saved-cart-list">
                    <li class="saved-cart-list-item cursor-pointer" v-for="(cart, index) in carts" :key="cart.id" v-on:click="selectSavedCart(cart.id, cart.bill_amount)" :class="{activeSavedCart : cart.id === selectedSavedCart}">
                        <div class="saved-cart-value-text value-no">
                            <p>{{index+1}}</p>
                        </div>
                        <div class="saved-cart-value-text value-invoice">
                            <p>{{cart.no_invoice}}</p>
                        </div>
                        <div class="saved-cart-value-text value-bill">
                            <p>Rp. {{cart.bill_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</p>
                        </div>
                        <div class="saved-cart-value-text value-notes">
                            <p>{{cart.note}}</p>
                        </div>
                        <div class="saved-cart-value-text value-time">
                            <p>{{getTimeFromDate(cart.created_at)}}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="right-wrapper col-4">
            <div class="cart-detail-list-container">
                <ul class="cart-detail-list">
                    <li class="cart-detail-list-item" v-for="cartDetail in cartDetails" :key="cartDetail.id" :class="{isPrinted : cartDetail.isPrinted === 1}">
                        <div class="cart-detail-text detail-name">
                            <h3>{{cartDetail.name}}</h3>
                        </div>
                        <div class="cart-detail-text detail-quantity">
                            <h3>(x{{cartDetail.qty}})</h3>
                        </div>
                        <div class="cart-detail-text detail-subtotal">
                            <h3>Rp. {{cartDetail.subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h3>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="action-container">
                <div class="penjualan-cart-price">
                            <div class="penjualan-cart-price-items">
                                <div class="penjualan-cart-price-items-title">
                                    <h4 style="margin: 0">Subtotal:</h4>
                                </div>
                                <div class="penjualan-cart-price-items-amount">
                                    <h4 style="margin: 0">Rp. {{ subtotal }}</h4>
                                </div>
                            </div>
                            <div class="penjualan-cart-price-items">
                                <div class="penjualan-cart-price-items-title">
                                    <h5 style="margin: 0">tax(%):</h5>
                                </div>
                                <div class="penjualan-cart-price-items-amount">
                                    <span class="minus-quantity-icon cursor-pointer" v-on:click.prevent="taxDecrement()">
                                        <svg-vue icon="minus-icon" v-if="taxAll > 0" style="width: 2rem; height: auto;"></svg-vue>
                                    </span>
                                    <div class="qtyAmount">
                                        <h4>{{ taxAll }}%</h4>
                                    </div>
                                    <span class="plus-quantity-icon cursor-pointer" v-on:click.prevent="taxIncrement()">
                                        <svg-vue icon="plus-icon" v-if="taxAll < 100" style="width: 2rem; height: auto;"></svg-vue>
                                    </span>
                                </div>
                            </div>
                            <div class="penjualan-cart-price-items">
                                <div class="penjualan-cart-price-items-title">
                                    <h5 style="margin: 0">Discount(%):</h5>
                                </div>
                                <div class="penjualan-cart-price-items-amount">
                                    <span class="minus-quantity-icon cursor-pointer" v-on:click.prevent="discountDecrement()">
                                        <svg-vue icon="minus-icon" v-if="discountAll > 0" style="width: 2rem; height: auto;"></svg-vue>
                                    </span>
                                    <div class="qtyAmount">
                                        <h4>{{ discountAll }}%</h4>
                                    </div>
                                    <span class="plus-quantity-icon cursor-pointer" v-on:click.prevent="discountIncrement()">
                                        <svg-vue icon="plus-icon" v-if="discountAll < 100" style="width: 2rem; height: auto;"></svg-vue>
                                    </span>
                                </div>
                            </div>
                            <div class="penjualan-cart-price-items">
                                <div class="penjualan-cart-price-items-title">
                                    <h3 style="margin: 0">Total:</h3>
                                </div>
                                <div class="penjualan-cart-price-items-amount">
                                    <h3 style="margin: 0">Rp. {{total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h3>
                                </div>
                            </div>
                        </div>
                <div class="action-list">
                    <button class="print-bill-button" @click="printBillAll(selectedSavedCart)">
                        <span>Print Bill (All)</span>
                    </button>
                    <button class="print-bill-button" @click="printBillRemain(selectedSavedCart)">
                        <span>Print Bill (Remain)</span>
                    </button>
                    <button class="checkout-button" @click="showSavedPaymentModal()">
                        <span>Charge</span>
                        <span v-if="tagihan !== 0">Rp. {{tagihan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="saved-payment-modal">
            <savedPaymentModal
                v-show="isSavedPaymentModalVisible"
                ref="savedPaymentModal"
                @closeSavedPaymentModal="closeSavedPaymentModal"
            />
        </div>
    </div>
</template>

<script>
import savedPaymentModal from './savedPaymentModal.vue'

export default {

    data() {
        return {
            //
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            carts: {},
            cartDetails: {},
            selectedSavedCart: '',
            tagihan: 0,
            taxAll: 0,
            discountAll: 0,
            isSavedPaymentModalVisible: false,
        }
    },
    components: {
        savedPaymentModal,
    },
    methods:{
        //
        getData(){
            //
            this.cartDetails= {},
            this.selectedSavedCart= '',
            this.tagihan= 0,
            this.taxAll= 0,
            this.discountAll= 0,
            this.isSavedPaymentModalVisible= false,
            axios.get('api/showSavedCart')
                .then((response)=>{
                this.carts = response.data.savedCarts;
            });
        },
        selectSavedCart(id, bill_amount){
            //
            this.$store.commit('clearSavedCart');
            this.selectedSavedCart = id;
            this.tagihan = bill_amount;
            axios.get('api/showSavedCartDetail/'+id)
            .then((response)=>{
                this.cartDetails = response.data.savedCartDetails;
                this.$store.commit('setSavedCart', response.data.savedCartDetails);
            });
            this.taxAll = 0;
            this.discountAll = 0;
        },
        getTimeFromDate(timestamp) {
            var date = new Date(timestamp);
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();

            var time = ("0" + hours).slice(-2)+":"+("0" + minutes).slice(-2)+":"+("0" + seconds).slice(-2)

            return time;
        },
        printBillAll(id){
            //
            axios.post('printBillAll/'+id)
                .then((response)=>{
                console.log('print bill (all) success')
                console.log(response)
            });
        },
        printBillRemain(id){
            //
            axios.post('printBillRemain/'+id)
                .then((response)=>{
                console.log('print bill (remain) success')
                console.log(response)
            });
        },
        taxDecrement(){
            //
            this.taxAll -= 5;
        },
        taxIncrement(){
            //
            this.taxAll += 5;
        },
        discountDecrement(){
            //
            this.discountAll -= 5;
        },
        discountIncrement(){
            //
            this.discountAll += 5;
        },
        showSavedPaymentModal() {
            let taxAll = this.taxAll;
            let discountAll = this.discountAll;
            this.isSavedPaymentModalVisible = true;
            this.$refs.savedPaymentModal.getData({taxAll, discountAll});
        },
        closeSavedPaymentModal() {
            this.isSavedPaymentModalVisible = false;
            this.getData();
        },
    },
    mounted(){
        //
        this.getData();
        this.$store.commit('clearSavedCart');
    },
    computed: {
        //

        subtotal() {
            let total = 0;

            for (let item of this.$store.state.savedCart) {
                if (item.specialPrice > 0) {
                    total += item.specialPrice*item.qty
                } else{
                    total += item.subtotal;
                }
            }

            return total.toFixed(2);
        },
        total() {
            let total = 0;

            for (let item of this.$store.state.savedCart) {
                if (item.specialPrice > 0) {
                    total += item.specialPrice*item.qty
                } else{
                    total += item.subtotal;
                }
            }

            let totalAll = total + (this.taxAll/100*total) - (this.discountAll/100*total);

            return totalAll.toFixed(2);
        },

    },

}
</script>

<style scoped>
    .flex-break{
        flex-basis: 100%;
        height: 0;
    }
    .main{
        height: 100%;
        width: 100%;
        min-width: 95vw;
    }
    .left-wrapper{
        background-color: #F9F9F9;
        border-radius: 10px;
        min-height: 90vh;
        margin: 1rem;
    }
    .saved-cart-container{
        margin: 1rem;
    }
    .saved-cart-title{
        display: flex;
        justify-content: center;
    }
    .saved-cart-header{
        display: flex;
        margin: 0 2rem 0 2rem;
    }
    .saved-cart-header-text{}
    .header-no{
        width: 5%;
    }
    .header-invoice{
        width: 25%;
    }
    .header-bill{
        width: 25%;
    }
    .header-notes{
        width: 30%;
    }
    .header-time{
        width: 15%;
    }
    .saved-cart-list{
        padding: 0;
        margin: 0 0 0 1rem;
        overflow-y: scroll;
        overflow-x: hidden;
    }
    .saved-cart-list-item{
        padding: 0 1rem 0 1rem;
        margin: 1rem 0 0 0;
        display: flex;
        border-radius: 10px;
        border: 1px solid #f5b247;
    }
    .saved-cart-list-item.activeSavedCart{
        background-color: #f5b247;
    }
    .value-no{
        width: 5%;
    }
    .value-invoice{
        width: 25%;
    }
    .value-bill{
        width: 25%;
    }
    .value-notes{
        width: 30%;
    }
    .value-time{
        width: 15%;
    }
    .right-wrapper{
        display: flex;
        margin: 1rem;
        flex-direction: column;
        min-height: 90vh;
        max-height: 90vh;
        overflow: hidden;
    }
    .cart-detail-list-container{
        background-color: #F9F9F9;
        min-height: 45vh;
        overflow-y: scroll;
        width: 100%;
        padding: 1rem;
        border-radius: 10px;
    }
    .cart-detail-list{
    }
    .cart-detail-list{
        padding: 0;
        margin: 1rem;
    }
    .cart-detail-list-item{
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid black;
    }
    .cart-detail-list-item.isPrinted{
        color: #0ee0ac;
    }
    .cart-detail-text{}
    .detail-name{
        display: flex;
        justify-content: flex-start;
        width: 40%;
    }
    .detail-quantity{
        display: flex;
        justify-content: center;
        width: 20%;
    }
    .detail-subtotal{
        display: flex;
        justify-content: flex-end;
        width: 40%;
    }
    .action-container{
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100%;
        max-height: 100%;
        background-color: #F9F9F9;
        border-radius: 10px;
    }
    .penjualan-cart-price-items{
        display: flex;
        margin: 0.3rem 1rem 0 1rem;
        justify-content: space-between;
        align-items: center;
    }
    .penjualan-cart-price-items-amount{
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
    }
    .action-list{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    .print-bill-button{
    display: inline-block;
    outline: 0;
    cursor: pointer;
    border: none;
    height: 5rem;
    width: 11.5rem;
    margin: 1rem;
    border-radius: 7px;
    background-color: #45ad8d;
    color: white;
    font-weight: 500;
    font-size: 20px;
    box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.1);
    transition: background 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;

    }
    .print-bill-button:hover{
        background: #0ee0ac;
        box-shadow: 0 6px 20px rgb(0 118 255 / 23%);
    }
    .checkout-button{

    display: inline-block;
    outline: 0;
    cursor: pointer;
    border: none;
    height: 5rem;
    width: 25rem;
    border-radius: 7px;
    background-color: #f5b247;
    color: white;
    font-weight: 500;
    font-size: 20px;
    box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.1);
    transition: background 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;

    }
    .checkout-button:hover{
        background: #ffc179;
        box-shadow: 0 6px 20px rgb(0 118 255 / 23%);
    }


</style>
