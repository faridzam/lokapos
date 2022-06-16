<template>
    <div class="modal-backdrop">
        <div class="modal">
            <header class="modal-header">
                <slot name="header">
                    Payment
                </slot>
                <button type="button" class="btn-close" @click="closeSavedPaymentModal">
                    x
                </button>
            </header>

            <section class="select-type">

            </section>

            <section class="modal-body">
                <slot name="body">

                    <div class="wrapper-left">
                        <div class="wrapper-left-method">
                            <div class="payment-methods" v-for="method in paymentMethods" :key="method.id" v-on:click="selectMethod(method.id)" :class="{activeMethod : method.id === selectedMethod}">
                                <input type="radio" name="select" :id="method.id" hidden>
                                <label v-bind:for="method.id" class="option-label">
                                <span class="payment-method-icon-span">
                                    <svg-vue :icon="method.name.split(' ')[0].toLowerCase()+'-icon'" title="payment method icon" style="height: 2rem; width: 2rem"></svg-vue>
                                </span>
                                <div class="flex-break"></div>
                                <span>{{method.name}}</span>
                            </label>
                            </div>
                        </div>
                    </div>

                    <div class="wrapper-center">

                        <div class="bill-amount-container">
                            <div class="bill-amount-box">
                                <h2 class="bill-amount-amount">Rp. {{tagihan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h2>
                                 <h3 class="bill-amount-title">TAGIHAN</h3>
                            </div>

                            <div class="bill-amount-box">
                                <h2 class="bill-amount-amount">Rp. {{kembalianCount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h2>
                                <h3 class="bill-amount-title">KEMBALIAN</h3>
                            </div>
                        </div>

                        <div class="form-group" v-for="k in Object.keys(inputsKeyboard)" :key="k">
                            <div class="payment-input" v-if="selectedMethod">
                                <div class="payment-input-container" @click.stop="showKeyboard">
                                    <h5 class="payment-input-field-title">{{k}}: </h5>
                                    <Input
                                        v-model="inputsKeyboard[k]"
                                        :inputs="inputsKeyboard"
                                        :inputName="k"
                                        @onInputFocus="onInputFocus"
                                        @onInputChange="onInputChange"
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="flex-break"></div>
                        </div>

                        <div class="payment-amount-container" v-if="selectedMethod === 1">
                            <div class="payment-amount-box payment-amount-100" @click="paymentAmount('100')">
                                <h3>Rp. 100,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-50" @click="paymentAmount('50')">
                                <h3>Rp. 50,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-20" @click="paymentAmount('20')">
                                <h3>Rp. 20,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-10" @click="paymentAmount('10')">
                                <h3>Rp. 10,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-5" @click="paymentAmount('5')">
                                <h3>Rp. 5,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-2" @click="paymentAmount('2')">
                                <h3>Rp. 2,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-1" @click="paymentAmount('1')">
                                <h3>Rp. 1,000</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-pas" @click="paymentAmount('pas')">
                                <h3>Uang Pas</h3>
                            </div>
                            <div class="payment-amount-box payment-amount-reset" @click="paymentAmount('reset')">
                                <h3>Reset</h3>
                            </div>
                        </div>

                    </div>

                    <div class="wrapper-right">
                        <div class="cart-list-container">
                            <ul class="cart-list">
                                <li class="cart-list-items" v-for="item in cart" :key="item">
                                    <div class="cart-list-items-left">
                                        <h3 class="cart-list-items-name">{{item.name}}</h3>
                                    </div>
                                    <div class="cart-list-items-center">
                                        <h3 class="cart-list-items-quantity">(x{{item.qty}})</h3>
                                    </div>
                                    <div class="cart-list-items-right">
                                        <h3 class="cart-list-items-price">Rp. {{item.subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}</h3>
                                    </div>

                                    <div class="flex-break"></div>

                                    <h5 class="cart-list-items-notes">{{item.note}}</h5>
                                </li>
                            </ul>
                        </div>

                        <div class="flex-break"></div>

                        <div class="cta-container">
                            <button class="checkout-button" @click="submitSavedPayment()">
                                <span class="cart-icon cursor-pointer">
                                    <svg-vue icon="cart-icon" style="width: 2rem; height: auto;"></svg-vue>
                                </span>
                                <h3>CHECKOUT</h3>
                            </button>
                        </div>
                    </div>

                </slot>
            </section>
        </div>
        <div class="flex-break"></div>
        <div class="payment-keyboard" v-show="isKeyboardVisible === 1 && inputName === 'note'" v-click-outside="hideKeyboard">
            <SimpleKeyboard
                @onChange="onChange"
                @onKeyPress="onKeyPress"
                :input="inputsKeyboard[inputName]"
                :inputName="inputName"
                :class="inputName"
            />
        </div>
    </div>
</template>

<script>
import SimpleKeyboard from "./SimpleKeyboardPayment";
import Input from "./InputPayment";

export default {
    name: 'savedPaymentModal',
    data(){
        return{
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            cart: [],
            paymentMethods: [],
            selectedMethod: '',
            tagihan: 0,
            kembalian: 0,
            inputsKeyboard:{
                bayar: "",
                note: "",
            },
            inputName: "note",
            isKeyboardVisible: 0,
            taxAll: 0,
            discountAll: 0,
        }
    },
    components: {
        SimpleKeyboard,
        Input
    },
    methods:{
        getData(payload){
            //
            this.getPaymentMethods();
            this.cart = this.$store.state.savedCart;
            this.selectedMethod = '';
            this.taxAll = payload.taxAll;
            this.discountAll = payload.discountAll;

            let total = 0;

            for (let item of this.$store.state.savedCart) {
                if (item.specialPrice > 0) {
                    total += item.specialPrice*item.qty
                } else{
                    total += item.subtotal;
                }
            }

            let totalAll = total + (this.taxAll/100*total) - (this.discountAll/100*total);

            this.tagihan = totalAll;
        },
        getPaymentMethods(){
            axios.get('/api/getPaymentMethods')
                 .then((response)=>{
                   this.paymentMethods = response.data.paymentMethods;
                   //select default category
                   //this.selectedMethods = response.data.paymentMethods[0].id;
                 });
        },
        selectMethod(id){

            //
            this.selectedMethod = id;

            if (id === 1) {
                this.inputsKeyboard['bayar'] = 0;
            } else{
                this.inputsKeyboard['bayar'] = this.tagihan;
            }
        },
        paymentAmount(code){
            //
            if (code === '100') {
                this.inputsKeyboard['bayar'] += 100000;
            }
            else if (code === '50') {
                this.inputsKeyboard['bayar'] += 50000;
            }
            else if (code === '20') {
                this.inputsKeyboard['bayar'] += 20000;
            }
            else if (code === '10') {
                this.inputsKeyboard['bayar'] += 10000;
            }
            else if (code === '5') {
                this.inputsKeyboard['bayar'] += 5000;
            }
            else if (code === '2') {
                this.inputsKeyboard['bayar'] += 2000;
            }
            else if (code === '1') {
                this.inputsKeyboard['bayar'] += 1000;
            }
            else if (code === 'pas') {
                this.inputsKeyboard['bayar'] = this.tagihan;
            }
            else if (code === 'reset') {
                this.inputsKeyboard['bayar'] = 0;
            }
            else{
                console.log('gagal menambahkan nominal');
            }
        },
        formatNumber(n){
            //
            this.specialPrice ="Rp. "+n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        closeSavedPaymentModal() {
            this.$emit('closeSavedPaymentModal');
        },
        submitSavedPayment(){

            if (this.selectedMethod !== '') {
                if (this.kembalian >= 0) {
                    const formData = new FormData();

                    formData.append("_token", this.csrf);
                    formData.append("cart", JSON.stringify(this.cart));
                    formData.append("paymentMethod", this.selectedMethod);
                    formData.append("tagihan", this.tagihan);
                    formData.append("kembalian", this.kembalian);
                    formData.append("bayar", this.inputsKeyboard['bayar']);
                    formData.append("note", this.inputsKeyboard['note']);
                    formData.append("taxAll", this.taxAll);
                    formData.append("discountAll", this.discountAll);
                    axios.post("/storeSavedOrder", formData)
                    .then(response => {
                        console.log('ok');
                        console.log(response.data);

                        this.$emit('closeSavedPaymentModal');
                        this.$store.commit('clearSavedCart');

                        Vue.$toast.open({
                            message: 'order checked out!',
                            type: 'success',
                            position: 'top',
                            duration: 2000,
                        });

                        this.$forceUpdate();
                    })
                    .catch(error => {
                        //
                        console.log('failed');

                        Vue.$toast.open({
                            message: 'error: payment failed!',
                            type: 'error',
                            position: 'top',
                            duration: 2000,
                        });
                    });
                } else{
                    Vue.$toast.open({
                        message: 'pay amount is not enaugh!',
                        type: 'error',
                        position: 'top',
                        duration: 2000,
                    });
                }
            } else{
                Vue.$toast.open({
                    message: 'please select payment method!',
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });
            }
        },

        showKeyboard(){
            //
            this.isKeyboardVisible = 1;
        },
        hideKeyboard(){
            // do something - probably hide the dropdown menu / modal etc.
            this.isKeyboardVisible = 0;
        },
        onChange(input) {
            this.inputsKeyboard[this.inputName] = input;
            this.$forceUpdate();
        },
        onKeyPress(button) {
            console.log("button", button);
            this.$forceUpdate();
        },
        onInputChange(input) {
            console.log("Input changed directly:", input.target.id);
            this.inputsKeyboard[input.target.id] = input.target.value;
            this.$forceUpdate();
        },
        onInputFocus(input) {
            console.log("Focused input:", input.target.id);
            this.inputName = input.target.id;
            this.$forceUpdate();
        },
    },
    mounted:{
        //
    },
    computed:{
        //
        kembalianCount() {
            this.kembalian =this.inputsKeyboard['bayar'] - this.tagihan;

            return this.kembalian;
        },
    },
    events: {
        clickOutsideKeyboard: function (event) {
            // do something - probably hide the dropdown menu / modal etc.
            console.log(event);
            this.hideKeyboard();
        },
    }
}
</script>

<style scoped>
    .flex-break{
        flex-basis: 100%;
        height: 0;
    }
    .modal-backdrop {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.3);
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
    }

    .modal {
        background: #FFFFFF;
        width: 90%;
        height: 90%;
        overflow-x: auto;
        display: flex;
        position: relative;
        flex-direction: column;
        border-radius: 10px;

        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        border: 0px solid rgba(255, 255, 255, 0.50);
    }
    .modal-header,
    .modal-footer {
        padding: 15px;
        display: flex;
    }
    .modal-header {
        position: relative;
        border-bottom: 1px solid #eeeeee;
        color: #f5b247;
        justify-content: space-between;
    }
    .modal-footer {
        border-top: 1px solid #eeeeee;
        flex-direction: column;
        justify-content: flex-end;
    }
    .modal-body {
        display: flex;
        width: 100%;
        height: 100%;
    }
    .wrapper-left{
        display: flex;
        margin: 1rem;
        min-width: 23vw;
        max-width: 25vw;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        border-radius: 10px;
    }
    .wrapper-left-method{
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        max-height: 75vh;
    }
    .payment-methods{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding-top: 1rem;
        width: 10rem;
        height: 5rem;
        margin: 1rem;
        color: #343434;
        border: 3px solid #f5b247;
        border-radius: 10px;
        font-weight: 600;

        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }
    .payment-methods.activeMethod{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding-top: 1rem;
        width: 10rem;
        height: 5rem;
        margin: 1rem;
        color: #eeeeee;
        font-weight: 700;
        background-image: linear-gradient(to bottom right, #ff7e5f, #feb47b);
        border: 3px solid #f5b247;
        border-radius: 10px;

        backdrop-filter: blur(1px);
        -webkit-backdrop-filter: blur(1px);
        box-shadow: 0 4px 30px rgba(241, 195, 68, 0.3);
    }
    .option-label{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }
    .payment-method-icon-span{
        margin: 0;
        padding: 0;
    }
    .wrapper-center{
        display: flex;
        flex-direction: column;
        margin: 1rem;
        min-width: 37vw;
        border-radius: 10px;
    }
    .bill-amount-container{
        display: flex;
        justify-content: space-evenly;
        margin: 1rem 0 0 0;
        width: 100%;
    }
    .bill-amount-box{
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 0;
        margin: 0 0 1rem 0;
        width: 40%;
        height: 8rem;
        color: #343434;
        background-color: #F4F4F4;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        border-radius: 10px;
    }
    .bill-amount-amount{
        margin:0;
        padding:0;
        width: 80%;
        border-bottom: 1px solid rgba(0, 0, 0, 0.10);
        font-weight: 800;
    }
    .bill-amount-title{
        margin:0;
        padding:0;
        width: 80%;
        text-align: right;
        font-weight: 400;
        font-size: 20px;
    }
    .form-group{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 1rem;
        width: 100%;
        height: fit-content;
    }
    .payment-input{
        display: flex;
    }
    .payment-input-container{
        display: flex;
        justify-content: space-between;
        width: 30vw;
        font-size: 20px;
        font-weight: 600;
    }
    .payment-input-container input{
        font-size: 20px;
        width: 20vw;
        font-weight: 600;
    }
    .payment-amount-container{
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin: 3rem 1rem 1rem 1rem;
        max-width: 100%;
    }
    .payment-amount-box{
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 1rem;
        width: 10rem;
        height: 5rem;
        color: white;
        box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
        border-radius: 10px;
    }
    .payment-amount-100{
        background-color: #F47C7C;
    }
    .payment-amount-50{
        background-color: #70A1D7;
    }
    .payment-amount-20{
        background-color: #A1DE93;
    }
    .payment-amount-10{
        background-color: #CA5FA6;
    }
    .payment-amount-5{
        background-color: #FFD59E;
    }
    .payment-amount-2{
        background-color: #92A9BD;
    }
    .payment-amount-1{
        background-color: #AAD8D3;
    }
    .payment-amount-pas{
        background-color: #A6CB12;
    }
    .payment-amount-reset{
        background-color: #E00543;
    }

    .wrapper-right{
        display: flex;
        flex-wrap: wrap;
        margin: 1rem;
        width: 100%;
        border-radius: 10px;
    }
    .cart-list-container{
        display: flex;
        width: 100%;
        min-height: 83%;
        box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
        border-radius: 10px;
    }
    .cart-list{
        display: block;
        width: 100%;
        margin: 1rem;
        padding: 0;
        max-height: 60vh;
        overflow-y: scroll;
    }
    .cart-list-items{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        list-style: none;
        margin-top: 1rem;
        padding-bottom: 1rem;
        color: rgb(44, 44, 44);
        border-bottom: 1px solid rgba(88, 88, 88, 0.1);
    }
    .cart-list-items-left{
        width: 10rem;
    }
    .cart-list-items-name{
        margin: 0;
        padding: 0;
    }
    .cart-list-items-notes{
        margin: 0;
        padding: 10px 0 0 0;
        color: rgb(88, 88, 88);
        font-weight: 500;
    }
    .cart-list-items-quantity{
        margin: 0;
        padding: 0;
    }
    .cart-list-items-price{
        margin: 0;
        padding: 0;
    }
    .cta-container{
        display: flex;
        align-items: flex-end;
        width: 100%;
        margin: 0;
        padding: 0;
        justify-content: center;
    }
    .checkout-button{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 5rem 0 0;
    outline: 0;
    cursor: pointer;
    border: none;
    height: 4rem;
    width: 75%;
    border-radius: 7px;
    background-color: #f5b247;
    color: white;
    font-weight: 500;
    font-size: 20px;
    box-shadow: 0 4px 14px 0 rgba(0, 0, 0, 0.1);
    transition: background 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;

    }
    .checkout-button:hover{
        background: #ffb259;
        box-shadow: 0 6px 20px rgb(0 118 255 / 23%);
    }
    .checkout-button:hover span.cart-icon{
        background-color: #ff8e0d;
    }
    .cart-icon{
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0 3rem 0 0;
        background-color: #eb932e;

        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        border-radius: 10px;
        fill: white !important;
        transition: background-color 0.2s ease,color 0.2s ease,box-shadow 0.2s ease;
    }
    .btn-close {
        position: absolute;
        top: 0;
        right: 0;
        border: none;
        font-size: 20px;
        padding: 10px;
        cursor: pointer;
        font-weight: bold;
        color: #f5b247;
        background: transparent;
    }
    .btn-submit {
        color: white;
        background: #f5b247;
        height: 2rem;
        border-radius: 10px;
        border: 0px solid rgba(255, 255, 255, 0.50);
    }
    .payment-keyboard{
        display: block;
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 27rem;
    }

</style>
