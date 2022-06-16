<template>
    <div class="container">
        <div class="penjualan-main row">
            <div class="penjualan-left col-8">
                <div class="penjualan-product">
                    <ul class="product-list row">
                        <li v-for="product in productFilter" :key="product.id" v-on:click="addToCart(product)" class="product-list-item col-1 cursor-pointer">
                            <div class="item-card">
                                <div class="item-card-left">

                                    <div class="item-card-title">
                                        <h4>{{ product.name }}</h4>
                                    </div>

                                    <div class="flex-break"></div>

                                    <div class="item-card-price">
                                        <h5>
                                            {{product.price}}
                                        </h5>
                                    </div>

                                </div>

                                <div class="item-card-right">

                                    <span class="item-card-right-image">
                                        <svg-vue icon="food-icon" title="food icon" style="width: 100px; height: auto; fill: #f5b247;"></svg-vue>
                                    </span>

                                </div>

                            </div>
                        </li>

                    </ul>
                </div>
                <div class="penjualan-category">

                    <div class="penjualan-category-title">
                        <h3>Categories:</h3>
                    </div>

                    <ul class="category-list">

                        <li v-for="category in categories" :key="category.id" v-on:click="selectCategory(category.id)" :class="{activeCategory : category.id === selectedCategory}" class="category-list-item cursor-pointer">
                            <div class="category-card">
                                <div class="category-card-title">
                                    <h4>{{category.name}}</h4>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="penjualan-right col-4">
                <div class="penjualan-cart">
                    <div class="penjualan-cart-items">
                        <!-- Cart ({{ $store.state.cartCount }}) -->

                        <div v-if="$store.state.cart.length > 0" class="cart-list">

                            <div v-for="item in $store.state.cart" :key="item.id" class="cart-list-item">

                            <div class="cart-list-item-details">
                                <div class="cart-list-item-details-left">
                                    <div class="cart-list-item-details-left-image">
                                        <span class="cart-item-span">
                                            <svg-vue icon="food-icon" title="cart menu icon" style="width: 3rem; height: auto"></svg-vue>
                                        </span>
                                    </div>
                                </div>
                                <div class="cart-list-item-details-right">
                                    <div class="cart-list-item-details-right-top">
                                        <h4>{{ item.name }}</h4>
                                        <div class="cart-list-item-details-right-top-quantity">
                                            <span class="minus-quantity-icon cursor-pointer" v-on:click.prevent="qtyDecrement(item)">

                                                <svg-vue icon="minus-icon" v-if="item.quantity > 1" style="width: 2rem; height: auto;"></svg-vue>
                                                <svg-vue v-else icon="trash-icon" title="Remove from cart" style="width: 2rem; height: auto; fill: #f45049;"></svg-vue>

                                            </span>
                                            <div class="qtyAmount">
                                                <h4>{{ item.quantity }}</h4>
                                            </div>
                                            <span class="plus-quantity-icon cursor-pointer" v-on:click.prevent="qtyIncrement(item)">
                                                <svg-vue icon="plus-icon" style="width: 2rem; height: auto;"></svg-vue>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="cart-list-item-details-right-middle">
                                        <div class="cart-list-item-details-right-middle-notes">
                                            <p>{{item.notes.join(' | ')}}</p>
                                        </div>
                                        <div v-if="item.discount > 0" class="cart-list-item-details-right-middle-discount">
                                            <p>discount: {{item.discount}}%</p>
                                        </div>
                                        <div v-if="item.specialPrice > 0" class="cart-list-item-details-right-middle-discount">
                                            <p>special price: Rp. {{item.specialPrice.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</p>
                                        </div>
                                    </div>

                                    <div class="cart-list-item-details-right-bottom">
                                        <div class="cart-list-item-details-right-bottom-action">
                                            <div class="cart-list-item-details-right-bottom-action-notes cursor-pointer" v-on:click.prevent="showNoteModal(item)">
                                                <p style="margin: 0; padding: 0; font-weight: bold;">notes&nbsp;&nbsp;</p>
                                                <span class="notes-icon-span">
                                                    <svg-vue icon="pencil-icon" style="width: 12px; height: auto;"></svg-vue>
                                                </span>
                                            </div>
                                            <div class="cart-list-item-details-right-bottom-action-discount cursor-pointer" v-on:click.prevent="showDiscountModal(item)">
                                                <p style="margin: 0; padding: 0; font-weight: bold;">discount&nbsp;&nbsp;</p>
                                                <span class="notes-icon-span">
                                                    <svg-vue icon="pencil-icon" style="width: 12px; height: auto;"></svg-vue>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="cart-list-item-details-right-bottom-subtotal">Rp. {{ item.totalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</div>
                                    </div>
                                </div>
                            </div>

                            </div>
                        </div>

                        <div v-else class="empty-cart-state">
                            <h1 class="empty-cart-state-text">
                                Cart is empty!
                            </h1>
                        </div>

                    </div>
                </div>
                <div class="penjualan-action">
                    <div class="penjualan-action-items">
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

                        <div class="penjualan-action-items-button">
                            <div class="penjualan-action-items-button-checkout">
                                <!-- <button class="print-bill-button">
                                    <span>Print Bill</span>
                                </button> -->
                                <button class="save-cart-button" @click="showSaveCartModal()">
                                    <span>Save</span>
                                </button>
                                <button class="checkout-button" @click="showPaymentModal()">
                                    <span>Charge Rp. {{total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="note-modal">
            <noteModal
            v-show="isNoteModalVisible"
            ref="noteModal"
            @closeNoteModal="closeNoteModal"
            />
        </div>
        <div class="discount-modal">
            <discountModal
            v-show="isDiscountModalVisible"
            ref="discountModal"
            @closeDiscountModal="closeDiscountModal"
            />
        </div>
        <div class="payment-modal">
            <paymentModal
            v-show="isPaymentModalVisible"
            ref="paymentModal"
            @closePaymentModal="closePaymentModal"
            />
        </div>
        <div class="save-cart-modal">
            <saveCartModal
            v-show="isSaveCartModalVisible"
            ref="saveCartModal"
            @closeSaveCartModal="closeSaveCartModal"
            />
        </div>
    </div>
</template>

<script>
import noteModal from './noteModal.vue';
import discountModal from './discountModal.vue';
import paymentModal from './paymentModal.vue';
import saveCartModal from './saveCartModal.vue';

export default {

    data() {
        return {

            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            categories: {},
            products: {},
            productCategory: {},
            selectedCategory: null,
            isNoteModalVisible: false,
            isDiscountModalVisible: false,
            isPaymentModalVisible: false,
            isSaveCartModalVisible: false,
            taxAll: 0,
            discountAll: 0,
        }
    },
    components: {
        noteModal,
        discountModal,
        paymentModal,
        saveCartModal,
    },
    methods: {
        getProducts(){
            axios.get('/api/penjualanLaravel')
                 .then((response)=>{
                   this.categories = response.data.categories;
                   this.products = response.data.products;
                   //select default category
                   this.selectedCategory = response.data.categories[0].id;
                 });
        },
        selectCategory(id){
            this.selectedCategory = id;
        },
        addToCart(item) {
            this.$store.commit('addToCart', item);
        },
        removeFromCart(item) {
            this.$store.commit('removeFromCart', item);
        },
        qtyDecrement(item){
            this.$store.commit('qtyDecrement', item);
        },
        qtyIncrement(item){
            this.$store.commit('qtyIncrement', item);
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
        // Create callback function to receive barcode when the scanner is already done
        onBarcodeScanned (barcode) {
            console.log(barcode)
            // do something...
            let item = this.products.find(product => product.product_code === barcode);
            this.$store.commit('addToCart', item);
        },
        // Reset to the last barcode before hitting enter (whatever anything in the input box)
        resetBarcode () {
            let barcode = this.$barcodeScanner.getPreviousCode()
            // do something...
        },

        showNoteModal(item) {
            this.isNoteModalVisible = true;
            this.$refs.noteModal.getData(item);
        },
        closeNoteModal() {
            this.isNoteModalVisible = false;
        },

        showDiscountModal(item) {
            this.isDiscountModalVisible = true;
            this.$refs.discountModal.getData(item);
        },
        closeDiscountModal() {
            this.isDiscountModalVisible = false;
        },
        showPaymentModal() {
            let taxAll = this.taxAll;
            let discountAll = this.discountAll;
            this.isPaymentModalVisible = true;
            this.$refs.paymentModal.getData({taxAll, discountAll});
        },
        closePaymentModal() {
            this.isPaymentModalVisible = false;
        },
        showSaveCartModal() {
            let taxAll = this.taxAll;
            let discountAll = this.discountAll;
            this.isSaveCartModalVisible = true;
            this.$refs.saveCartModal.getData({taxAll, discountAll});
        },
        closeSaveCartModal() {
            this.isSaveCartModalVisible = false;
        },
    },
    mounted(){
        //
    },
    computed: {

        productFilter() {
            return this.selectedCategory ?
                this.products.filter((product) => product.category_id == this.selectedCategory) :
                this.products;
        },
        subtotal() {
            let total = 0;

            for (let item of this.$store.state.cart) {
                if (item.specialPrice > 0) {
                    total += item.specialPrice*item.quantity
                } else{
                    total += item.totalPrice;
                }
            }

            return total.toFixed(2);
        },
        total() {
            let total = 0;

            for (let item of this.$store.state.cart) {
                if (item.specialPrice > 0) {
                    total += item.specialPrice*item.quantity
                } else{
                    total += item.totalPrice;
                }
            }

            let totalAll = total + (this.taxAll/100*total) - (this.discountAll/100*total);

            return totalAll.toFixed(2);
        },

    },
    created() {
        this.getProducts();
        // Add barcode scan listener and pass the callback function
        this.$barcodeScanner.init(this.onBarcodeScanned);
    },
    destroyed () {
      // Remove listener when component is destroyed
      this.$barcodeScanner.destroy()
    },

}
</script>

<style scoped>

    .flex-break{
        flex-basis: 100%;
        height: 0;
    }

    .penjualan-main{

        height: 100%;
        width: 100%;
        min-width: 95vw;

    }
    .penjualan-left{
        background-color: #f0f0f0;
        border-radius: 10px;
        height: fit-content;
    }
    .penjualan-right{
        background-color: #f0f0f0;
        border-radius: 10px;
        height: fit-content;
    }
    .penjualan-product{
        margin: 10px;
        background-color: #F9F9F9;
        border-radius: 10px;
        min-height: 79vh;
    }
    .penjualan-category{
        display: flex;
        align-items: center;
        margin: 10px;
        background-color: #F9F9F9;
        border-radius: 10px;
        min-height: 10vh;
    }
    .penjualan-category-title{
        width:fit-content;
        margin-left: 1rem;
    }

    .product-list{
        margin: 0;
        padding: 0;
        overflow-y: scroll;
        max-height: 79vh;
        max-width: 60vw;
        display: flex;
        flex-wrap: wrap;
        list-style: none;
    }
    .product-list-item{
        width: auto;
        height: auto;
        padding: 1rem;
    }
    .category-list{
        display: flex;
        flex-direction: row;
        margin: 0 0 0 1rem;
        padding: 0;
        width: 100%;
        overflow-x: scroll;
        list-style: none;
    }
    .category-list-item{
        display: flex;
        justify-content: center;
        margin: 1rem 1rem 0 0;
        width: 100%;
        height: 100%;
        min-width: 10rem;
        max-width: 15rem;
        cursor: pointer;
        background-color: white;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    .category-list-item:hover{
        background-color: #f8c575;
    }

    .category-list-item.activeCategory{
        display: flex;
        justify-content: center;
        margin: 1rem 1rem 0 0;
        width: 100%;
        height: 100%;
        min-width: 10rem;
        max-width: 15rem;
        background-color: #f5b247;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .penjualan-cart{
        margin: 10px;
        max-height: 60vh;
        min-height: 60vh;
        overflow-y: scroll;
        background-color: #F9F9F9;
    }
    .penjualan-cart-items{

    }
    .penjualan-action{
        margin: 10px;
        min-height: 29vh;
        background-color: #F9F9F9;
    }
    .penjualan-action-items{
        padding: 1rem;
    }
    .penjualan-cart-price-items{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .penjualan-cart-price-items-amount{
        display: flex;
        align-items: center;
        padding: 0;
        margin: 0;
    }

    .penjualan-action-items-button{
        margin-top: 2rem;
    }

    .item-card{
        display: flex;
        justify-content: space-between;
        width: 100%;
        height: 100%;
        max-width: 20rem;
        background-color: white;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    .item-card-left{
        display: flex;
        flex-wrap: wrap;
        padding: 0 0 0 1rem;
    }
    .item-card-right{
        display: flex;
        flex-wrap: wrap;
        padding: 0 1rem 0 0;
    }
    .item-card-right-image{
        display: flex;
        align-items: center;
    }
    .item-card-title{
        width: 10rem;
        height: 5rem;
        display: flex;
        flex-wrap: wrap;
    }

    .cart-list{
    }
    .cart-list-item{
        display: block;
        width: auto;
    }
    .cart-list-item-details{
        display: flex;
        align-items: center;
        margin: 1rem;
        background-color: #ffffff;
        border-radius: 10px;

        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.50);
    }
    .cart-list-item-details-left{
        display: flex;
    }
    .cart-list-item-details-left-image{
        margin: 1rem;
    }
    .cart-item-span{
        fill: #f5b247;
    }
    .cart-list-item-details-right{
        display: block;
        width: 100%;
    }
    .cart-list-item-details-right-top{
        display: flex;
        margin-left: 1rem;
        margin-right: 1rem;
        justify-content: space-between;
        width: auto;
    }
    .cart-list-item-details-right-top-quantity{
        display: flex;
        align-items: center;
    }
    .cart-list-item-remove {
    }
    .minus-quantity-icon{
        margin-right: 1rem;
    }
    .plus-quantity-icon{
        margin-left: 1rem;
    }

    .cart-list-item-details-right-middle{
        display: flex;
        justify-content: space-between;
        margin-left: 1rem;
        margin-right: 1rem;
        color: grey;
    }
    .cart-list-item-details-right-bottom{
        display: flex;
        align-items: center;
        margin-left: 1rem;
        margin-right: 1rem;
        margin-bottom: 1rem;
        justify-content: space-between;
        width: auto;
    }
    .cart-list-item-details-right-bottom-action{
        display: flex;
    }
    .cart-list-item-details-right-bottom-action-notes{
        display: flex;
        align-items: flex-start;
        padding: 5px 10px 5px 10px;
        margin: 10px;
        background-color: #f5b247;
        border-radius: 10px;
    }
    .cart-list-item-details-right-bottom-action-discount{
        display: flex;
        align-items: flex-start;
        padding: 5px 10px 5px 10px;
        margin: 10px;
        background-color: #f5b247;
        border-radius: 10px;
    }
    .empty-cart-state{
        display: flex;
        margin-top: 50%;
        align-items: center;
        justify-content: center;
    }
    .empty-cart-state-text{
        color: rgba(20, 20, 20, 0.2);
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
    .save-cart-button{

    display: inline-block;
    outline: 0;
    cursor: pointer;
    border: none;
    height: 5rem;
    width: 6rem;
    border-radius: 7px;
    background-color: #45ad8d;
    color: white;
    font-weight: 500;
    font-size: 20px;
    box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.1);
    transition: background 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;

    }
    .save-cart-button:hover{
        background: #0ee0ac;
        box-shadow: 0 6px 20px rgb(0 118 255 / 23%);
    }
    .print-bill-button{

    display: inline-block;
    outline: 0;
    cursor: pointer;
    border: none;
    height: 5rem;
    width: 6rem;
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
    .penjualan-action-items-button-checkout{
        display: flex;
        justify-content: space-between;
    }
</style>
