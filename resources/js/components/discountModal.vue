<template>
    <div class="modal-backdrop">
        <div class="modal">
            <header class="modal-header">
                <slot name="header">
                    {{itemDiscountModal.name}}
                </slot>
                <button type="button" class="btn-close" @click="closeDiscountModal">
                    x
                </button>
            </header>

            <section class="select-type">
                <div class="wrapper">
                    <input type="radio" name="select" id="option-1" checked>
                    <input type="radio" name="select" id="option-2">
                    <label for="option-1" class="option option-1" v-on:click="changeCategory(0)">
                        <div class="dot"></div>
                        <span>discount(%)</span>
                    </label>
                    <label for="option-2" class="option option-2" v-on:click="changeCategory(1)">
                        <div class="dot"></div>
                        <span>Special Price</span>
                    </label>
                </div>
            </section>

            <section class="modal-body">
                <slot name="body">

                    <div class="form-group" v-for="k in Object.keys(inputsKeyboard)" :key="k">
                        <div class="discount-input-field" v-if="k === type">
                            <div class="notes-input-container">
                                <h5 class="discount-input-field-title">{{k}}: </h5>
                                <Input
                                    v-model="inputsKeyboard[k]"
                                    :inputs="inputsKeyboard"
                                    :inputName="k"
                                    @onInputFocus="onInputFocus"
                                    @onInputChange="onInputChange"
                                />
                                <h5 class="discount-input-field-percent" v-if="type === 'discount'">( % )</h5>
                            </div>
                        </div>
                    </div>

                </slot>
            </section>

            <footer class="modal-footer">
                <slot name="footer">
                    <button type="button" class="btn-submit" v-if="type === 'discount'" v-on:click.prevent="submitDiscountModal()">
                        Add Discount
                    </button>
                    <button type="button" class="btn-submit" v-if="type === 'specialPrice'" v-on:click.prevent="submitDiscountModal()">
                        Add Special Price
                    </button>
                </slot>
            </footer>
        </div>
        <div class="flex-break"></div>
        <div class="discount-keyboard">
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
import SimpleKeyboard from "./SimpleKeyboardDiscount";
import Input from "./InputDiscount";

export default {
    name: 'discountModal',
    data(){
        return{
            itemDiscountModal: null,
            type: 'discount',
            inputsKeyboard:{
                discount: "",
                specialPrice: "",
            },
            inputName: "discount",
        }
    },
    components: {
        //
        SimpleKeyboard,
        Input
    },
    methods:{
        getData(item){
            //
            this.itemDiscountModal = item;
        },
        changeCategory(type){
            //
            if (type === 0) {
                this.type = 'discount';
                this.inputName = 'discount';
            } else if( type === 1 ){
                this.type = 'specialPrice';
                this.inputName = 'specialPrice';
            } else{
                console.log('change category failed');
            }

        },
        formatNumber(n){
            //
            this.specialPrice ="Rp. "+n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        closeDiscountModal() {
            this.$emit('closeDiscountModal');
        },
        submitDiscountModal(){
            let item = this.itemDiscountModal;

            if (this.type === 'discount') {
                //
                let inputDiscount = this.inputsKeyboard.discount;

                this.$store.commit('submitDiscountModal', {
                    item,
                    inputDiscount
                });

            } else if (this.type === 'specialPrice') {
                //
                let inputSpecialPrice = this.inputsKeyboard.specialPrice;

                this.$store.commit('submitSpecialPriceModal', {
                    item,
                    inputSpecialPrice
                });

            } else{
                console.log('submit discount modal failed');
            }
            this.$emit('closeDiscountModal');
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
    computed:{
        //
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
        width: 30%;
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
        display: block;
        margin: auto;
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
        background: #f5b247;
        height: 4rem;
        font-size: 20px;
        border-radius: 10px;
        border: 0px solid rgba(255, 255, 255, 0.50);
    }
    .select-type{
        display: flex;
    }
    .wrapper{
    display: flex;
    background: #fff;
    height: 2rem;
    width: 100%;
    align-items: center;
    justify-content: space-evenly;
    border-radius: 5px;
    padding: 20px 15px;
    }
    .wrapper .option{
    background: #fff;
    height: 100%;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-evenly;
    margin: 0 10px;
    border-radius: 5px;
    cursor: pointer;
    padding: 0 10px;
    border: 2px solid lightgrey;
    transition: all 0.3s ease;
    }
    .wrapper .option .dot{
    height: 20px;
    width: 20px;
    background: #d9d9d9;
    border-radius: 50%;
    position: relative;
    }
    .wrapper .option .dot::before{
    position: absolute;
    content: "";
    top: 4px;
    left: 4px;
    width: 12px;
    height: 12px;
    background: #f5b247;
    border-radius: 50%;
    opacity: 0;
    transform: scale(1.5);
    transition: all 0.3s ease;
    }
    input[type="radio"]{
    display: none;
    }
    #option-1:checked:checked ~ .option-1,
    #option-2:checked:checked ~ .option-2{
    border-color: #f5b247;
    background: #f5b247;
    }
    #option-1:checked:checked ~ .option-1 .dot,
    #option-2:checked:checked ~ .option-2 .dot{
    background: #fff;
    }
    #option-1:checked:checked ~ .option-1 .dot::before,
    #option-2:checked:checked ~ .option-2 .dot::before{
    opacity: 1;
    transform: scale(1);
    }
    .wrapper .option span{
    font-size: 20px;
    color: #808080;
    }
    #option-1:checked:checked ~ .option-1 span,
    #option-2:checked:checked ~ .option-2 span{
    color: #fff;
    }

    .discount-input-field{
        display: flex;
        align-items: center;
        width: 100%;
    }
    .notes-input-container{
        display: flex;
        align-items: center;
        height: 3rem;
    }
    .notes-input-container input{
        height: 3rem;
        max-width: 10rem;
        font-size: 24px;
        font-weight: 600;
    }
    .discount-input-field-input{
        height: 2rem;
        font-size: 20px;
    }
    .discount-input-field-title{
        font-size: 1rem;
        margin: 0 1rem 0 0;
    }
    .discount-input-field-percent{
        font-size: 1rem;
        margin: 0 0 0 1rem;
    }
    .discount-keyboard{
        display: block;
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 27rem;
    }
</style>
