<template>
    <div class="modal-backdrop">
        <div class="modal">
            <header class="modal-header">
                <slot name="header">
                    Close Order
                </slot>
                <button type="button" class="btn-close" @click="closeCloseOrderModal">
                    x
                </button>
            </header>

            <section class="modal-body">
                <slot name="body">
                    <h3>Fraction of Money</h3>
                    <div class="form-group" v-for="k in Object.keys(inputsKeyboard)" :key="k">
                        <h3 v-if="k === 'quantity'">Close Order Receipt Quantity</h3>
                        <div class="notes-input-container" :class="k">
                            <label class="input-label" :for="k">{{k}}</label>
                            <div class="input-field">
                                <span class="minus-input-icon" @click="dec(k)" v-longclick="() => dec(k)" style="margin: 0 2rem 0 2rem">
                                    <svg-vue icon="minus-icon" style="width: 2rem; height: auto;"></svg-vue>
                                </span>
                                <div @click.stop="showKeyboard">
                                    <Input
                                    v-model="inputsKeyboard[k]"
                                    :inputs="inputsKeyboard"
                                    :inputName="k"
                                    @onInputFocus="onInputFocus"
                                    @onInputChange="onInputChange"
                                />
                                </div>
                                <span class="plus-input-icon" @click="inc(k)" v-longclick="() => inc(k)" style="margin: 0 2rem 0 2rem">
                                    <svg-vue icon="plus-icon" style="width: 2rem; height: auto;"></svg-vue>
                                </span>
                            </div>
                        </div>
                    </div>
                </slot>
            </section>

            <footer class="modal-footer">
                <slot name="footer">
                </slot>
                <button type="button" class="btn-submit" v-on:click.prevent="submitCloseOrderModal()">
                    Close Order Rp. {{total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")}}
                </button>
            </footer>
        </div>
        <div class="flex-break"></div>
        <!-- <div class="close-order-keyboard" v-show="isKeyboardVisible === 1" v-click-outside="hideKeyboard">
            <SimpleKeyboard
                @onChange="onChange"
                @onKeyPress="onKeyPress"
                :input="inputsKeyboard[inputName]"
                :inputName="inputName"
                :class="inputName"
                :id="inputName"
            />
        </div> -->
  </div>
</template>

<script>
//import SimpleKeyboard from "./SimpleKeyboardCloseOrder";
import axios from "axios";
import Input from "./InputCloseOrder";

export default {
    name: 'closeOrderModal',
    data(){
        return{
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            inputsKeyboard:{
                pecahan100k: 0,
                pecahan50k: 0,
                pecahan20k: 0,
                pecahan10k: 0,
                pecahan5k: 0,
                pecahan2k: 0,
                pecahan1k: 0,
                quantity: 1,
            },
            inputName: "pecahan100k",
        }
    },
    components: {
        //SimpleKeyboard,
        Input,
    },
    methods: {

        getData(){
            //
            this.inputName = "pecahan100k";
            this.inputsKeyboard = {
                pecahan100k: 0,
                pecahan50k: 0,
                pecahan20k: 0,
                pecahan10k: 0,
                pecahan5k: 0,
                pecahan2k: 0,
                pecahan1k: 0,
                quantity: 1,
            }
        },

        closeCloseOrderModal() {
            this.$emit('closeCloseOrderModal');
        },
        submitCloseOrderModal() {
            this.$swal({title: 'Close Order Confirmation',
            text: "are you sure want to closing order?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes!',
            cancelButtonText: 'No!',
            reverseButtons: true
            }).then((result) => {
            if (result.isConfirmed) {

                const formData = new FormData();

                formData.append("_token", this.csrf);
                formData.append('pecahan100', this.inputsKeyboard['pecahan100k']);
                formData.append('pecahan50', this.inputsKeyboard['pecahan50k']);
                formData.append('pecahan20', this.inputsKeyboard['pecahan20k']);
                formData.append('pecahan10', this.inputsKeyboard['pecahan10k']);
                formData.append('pecahan5', this.inputsKeyboard['pecahan5k']);
                formData.append('pecahan2', this.inputsKeyboard['pecahan2k']);
                formData.append('pecahan1', this.inputsKeyboard['pecahan1k']);
                formData.append('total', this.total);
                formData.append('quantity', this.inputsKeyboard['quantity']);

                // let timerInterval
                // Swal.fire({
                //     title: 'Please Wait!',
                //     html: '<b></b> %',
                //     timer: 2000,
                //     didOpen: () => {
                //         Swal.showLoading()
                //         const b = Swal.getHtmlContainer().querySelector('b')
                //         timerInterval = setInterval(() => {
                //             b.textContent = (2000 - Swal.getTimerLeft())/20
                //         }, 100)
                //     },
                //     willClose: () => {
                //         clearInterval(timerInterval)
                //     }
                //     }).then((result) => {
                //     /* Read more about handling dismissals below */
                //     // if (result.dismiss === Swal.DismissReason.timer) {
                //     //     console.log('I was closed by the timer')
                //     // }
                // })

                axios.post("/closeOrder", formData)
                .then(response => {
                    Vue.$toast.open({
                        type: 'success',
                        position: 'top',
                        duration: 2000,
                    });
                    console.log('ok');
                    console.log(response.data);
                    this.$emit('closeCloseOrderModal');
                    //window.location.href = "/login";
                })
                .catch(error => {
                    //
                    console.log('failed');
                });
            } else{
                //
            }
            })
        },

        inc(index){
            if (index === 'quantity') {
                if (this.inputsKeyboard[index] < 5) {
                    this.inputsKeyboard[index]++;
                } else{
                    console.log("can't more than 5")
                }
            } else{
                this.inputsKeyboard[index]++;
            }
        },
        dec(index){
            if (this.inputsKeyboard[index] > 0) {
                this.inputsKeyboard[index]--;
            }
        },

        // showKeyboard(){
        //     this.isKeyboardVisible = 1;
        // },
        // hideKeyboard(){
        //     this.isKeyboardVisible = 0;
        // },
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
        //
        total(){
            //
            var total = (this.inputsKeyboard['pecahan100k']*100000)+(this.inputsKeyboard['pecahan50k']*50000)+(this.inputsKeyboard['pecahan20k']*20000)+(this.inputsKeyboard['pecahan10k']*10000)+(this.inputsKeyboard['pecahan5k']*5000)+(this.inputsKeyboard['pecahan2k']*2000)+(this.inputsKeyboard['pecahan1k']*1000);
            return total.toFixed(2);
        },
    },
    watch:{
        //
    },
    events: {
        // clickOutsideKeyboard: function (event) {
        //     console.log(event);
        //     this.hideKeyboard();
        // },
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
    width: 50%;
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

  .modal-body {
      display: block;
      margin: auto;
      width: 80%;
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
  .notes-input-container{
      display: flex;
      justify-content: space-between;
      margin: 10px;
  }
  .input-label{
    display: flex;
    justify-content: center;
    margin: 0.5rem;
    font-weight: 500;
    font-size: 20px;
  }
  .input-field{
    display: flex;
    justify-content: center;
    align-items: center;
  }
  input{
      margin: 0 0 0 0;
      height: 3rem;
      width: 5rem;
      font-size: 20px;
      font-weight: 700;
      border-radius: 10px;
      border: 1px solid rgba(0, 0, 0, 0.2);
  }
  input:focus{
      outline: none;
      border: 1px solid rgba(0, 0, 0, 0.2);
  }
    .note-keyboard{
        display: block;
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 27rem;
    }

</style>
