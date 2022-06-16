<template>
    <div class="modal-backdrop">
        <div class="modal">
            <header class="modal-header">
                <slot name="header">
                    Save Cart
                </slot>
                <button type="button" class="btn-close" @click="closeSaveCartModal">
                    x
                </button>
            </header>

            <section class="modal-body">
                 <slot name="body">
                    <div class="select-form">
                        <h5>select invoice number</h5>
                        <v-select :options="options" label="no_invoice" v-model="selectedSavedCart"></v-select>
                        <!-- <select class="select-form-list">
                            <option class="select-form-list-item-center" value="new" selected>CREATE NEW!!!</option>
                            <option class="select-form-list-item" v-for="item in savedCarts" :key="item.id" :value="item.id">{{item.no_invoice}}</option>
                        </select> -->
                    </div>

                    <div class="form-group" v-for="k in Object.keys(inputsKeyboard)" :key="k">
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
                </slot>
            </section>

            <footer class="modal-footer">
                <slot name="footer">
                </slot>
                <div class="action-container">
                    <button type="button" class="btn-submit save-button" @click="submitSaveCartModal()">
                        Save Cart
                    </button>
                    <button type="button" class="btn-submit save-print-button" @click="submitSaveCartPrintModal()">
                        Save Cart & Print Bill
                    </button>
                </div>
            </footer>
        </div>
        <div class="flex-break"></div>
        <div class="save-cart-keyboard" v-show="isKeyboardVisible === 1 && inputName === 'notes'" v-click-outside="hideKeyboard">
            <SimpleKeyboard
                @onChange="onChange"
                @onKeyPress="onKeyPress"
                :input="inputsKeyboard[inputName]"
                :inputName="inputName"
                :class="inputName"
                :id="inputName"
            />
        </div>
  </div>
</template>

<script>
import SimpleKeyboard from "./SimpleKeyboardSaveCart";
import Input from "./InputSaveCart";

export default {
    name: 'saveCartModal',
    data(){
        return{
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            carts: {},
            savedCarts:{},
            inputsKeyboard:{
                notes: "",
            },
            inputName: "notes",
            tagihan: 0,
            isKeyboardVisible: 0,
            taxAll: 0,
            discountAll: 0,
            selectedSavedCart: {id: 0, no_invoice: "CREATE NEW!!!", note: 0, bill_amount: 0},
        }
    },
    components:{
        SimpleKeyboard,
        Input
    },
    methods:{
        getData(payload){
            axios.get('api/showSavedCart')
                .then((response)=>{
                this.savedCarts = response.data.savedCarts;
            });

            let total = 0;

            for (let item of this.$store.state.cart) {
                if (item.specialPrice > 0) {
                    total += item.specialPrice*item.quantity
                } else{
                    total += item.totalPrice;
                }
            }

            let totalAll = total + (this.taxAll/100*total) - (this.discountAll/100*total);

            this.carts = this.$store.state.cart;
            this.tagihan = totalAll;
            this.taxAll = payload.taxAll;
            this.discountAll = payload.discountAll;
            this.selectedSavedCart = {id: 0, no_invoice: "CREATE NEW!!!", note: 0, bill_amount: 0};
            this.inputsKeyboard['notes'] = "";
        },
        closeSaveCartModal() {
            this.$emit('closeSaveCartModal');
        },
        submitSaveCartModal() {
            //
            const formData = new FormData();
            formData.append("_token", this.csrf);
            formData.append("cart", JSON.stringify(this.carts));
            formData.append("selectedExisting", JSON.stringify(this.selectedSavedCart));
            formData.append("tagihan", this.tagihan);
            formData.append("note", this.inputsKeyboard['notes']);
            formData.append("taxAll", this.taxAll);
            formData.append("discountAll", this.discountAll);
            axios.post("/saveCart", formData)
            .then(response => {

                Vue.$toast.open({
                    message: 'cart saved!',
                    type: 'success',
                    position: 'top',
                    duration: 2000,
                });

                console.log('ok');
                console.log(response.data);
                this.$emit('closeSaveCartModal');
                this.$store.commit('clearCart');
            })
            .catch(error => {
                //
                console.log('failed');

                Vue.$toast.open({
                    message: 'cart not saved!',
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });

            });
        },
        submitSaveCartPrintModal() {
            //
            const formData = new FormData();

            formData.append("_token", this.csrf);
            formData.append("cart", JSON.stringify(this.carts));
            formData.append("selectedExisting", JSON.stringify(this.selectedSavedCart));
            formData.append("tagihan", this.tagihan);
            formData.append("note", this.inputsKeyboard['notes']);
            formData.append("taxAll", this.taxAll);
            formData.append("discountAll", this.discountAll);
            axios.post("/saveCartPrint", formData)
            .then(response => {

                Vue.$toast.open({
                    message: 'cart saved & printed!',
                    type: 'success',
                    position: 'top',
                    duration: 2000,
                });

                console.log('ok');
                console.log(response.data);
                this.$emit('closeSaveCartModal');
                this.$store.commit('clearCart');
            })
            .catch(error => {
                //
                console.log('failed');
                Vue.$toast.open({
                    message: 'cart not saved & not printed!',
                    type: 'error',
                    position: 'top',
                    duration: 2000,
                });
            });
        },
        showKeyboard(){
            this.isKeyboardVisible = 1;
        },
        hideKeyboard(){
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
    mounted() {
        //
    },
    computed:{
        options(){
            let Obj = this.savedCarts;
            var result=[];
            result.push({id: 0, no_invoice: "CREATE NEW!!!", note: 0, bill_amount: 0});
            for(var i=0;i<Obj.length;i++){
                result.push({id: Obj[i].id, no_invoice: Obj[i].no_invoice, note: Obj[i].note, bill_amount: Obj[i].bill_amount});
            }

            return result;
        }
    },
    watch:{
        //
    },
    events: {
        clickOutsideKeyboard: function (event) {
            console.log(event);
            this.hideKeyboard();
        },
    },
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
        width: 40%;
        overflow-x: auto;
        display: flex;
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
    .action-container{
        display: flex;
        justify-content: space-between;
    }
    .modal-body {
        display: block;
        margin: auto;
        width: 70%;
        padding: 20px 20px 20px 20px;
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
        height: 4rem;
        font-size: 20px;
        border-radius: 10px;
        border: 0px solid rgba(255, 255, 255, 0.50);
    }
    .save-button{
        background: #45ad8d;
        width: 15rem;
    }
    .save-print-button{
        background: #f5b247;
        width: 30rem;
    }
    .payment-input-container input{
        width: 100%;
        height: 3rem;
        font-size: 20px;
        font-weight: 500;
    }
    .save-cart-keyboard{
        display: block;
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 27rem;
    }

</style>
