<template>
    <div class="deposit-form-container">

        <form @submit="onSubmit" class="deposit-form" action="/deposit" method="POST">

            <input type="text" name="_token" id="_token" :value="csrf" hidden>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 100.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement100" style="width:40px"> <svg-vue icon="minus-icon" v-if="count100 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count100" name="pec100" id="pec100" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment100"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string100" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 50.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement50" style="width:40px"> <svg-vue icon="minus-icon" v-if="count50 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count50" name="pec50" id="pec50" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment50"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string50" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 20.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement20" style="width:40px"> <svg-vue icon="minus-icon" v-if="count20 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count20" name="pec20" id="pec20" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment20"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string20" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 10.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement10" style="width:40px"> <svg-vue icon="minus-icon" v-if="count10 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count10" name="pec10" id="pec10" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment10"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string10" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 5.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement5" style="width:40px"> <svg-vue icon="minus-icon" v-if="count5 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count5" name="pec5" id="pec5" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment5"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string5" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 2.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement2" style="width:40px"> <svg-vue icon="minus-icon" v-if="count2 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count2" name="pec2" id="pec2" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment2"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string2" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

            <div class="deposit-calculator-pecahan">
                <div class="pecahan-title">
                    <h2>Rp. 1.000</h2>
                </div>
                <div class="qty main-form-pecahan">
                    <span class="minus" v-on:click.prevent="decrement1" style="width:40px"> <svg-vue icon="minus-icon" v-if="count1 > 0" style="width: 40px; height: auto;"></svg-vue> </span>
                    <input type="number" class="count" v-model="count1" name="pec1" id="pec1" value=0 readonly>
                    <span class="plus" v-on:click.prevent="increment1"> <svg-vue icon="plus-icon" style="width: 40px; height: auto;"></svg-vue> </span>
                    <div class="pecahan-total-form">
                        <input type="text" v-model="string1" class="pecahan-total-string" name="pecahan-total-string" id="pecahan-total-string" placeholder="Rp. 0" readonly>
                    </div>
                </div>
            </div>

            <div class="flex-break"></div>

                <div class="deposit-calculator-pecahan" style="width: 100%; display:flex; justify-content: flex-end;">

                    <button id="sub" type="submit" class="button-17" style="margin-left: 5rem; margin-right: auto; height: 5rem; width: 15rem; border-radius: 2rem; font-size: 1.5rem; font-weight: bolder; letter-spacing: 4px;" value="Submit">SUBMIT!</button>


                    <div class="total-title" style="margin: 0; padding: 0; text-align: left; font-size: 2rem; color: #FFFAF0;">
                        <h2 style="">Total: </h2>
                    </div>

                    <input type="number" v-model="total" class="nominal-deposit" name="nominal" id="nominal" placeholder="Rp. 0" readonly hidden>
                    <input type="text" v-model="stringTotal" class="pecahan-total-string" name="nominal-string" id="nominal-string" placeholder="Rp. 0" readonly>
                </div>

            <div class="flex-break"></div>

        </form>

    </div>
</template>

<script>

export default {
    props: {
    //   user: {
    //     type: Number,
    //     required: true
    //   }
    },
    data: ()=> {
        return {
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),

            count100: 0,
            total100: 0,
            string100: 'Rp. 0',

            count50: 0,
            total50: 0,
            string50: 'Rp. 0',

            count20: 0,
            total20: 0,
            string20: 'Rp. 0',

            count10: 0,
            total10: 0,
            string10: 'Rp. 0',

            count5: 0,
            total5: 0,
            string5: 'Rp. 0',

            count2: 0,
            total2: 0,
            string2: 'Rp. 0',

            count1: 0,
            total1: 0,
            string1: 'Rp. 0',

            total: 0,
            stringTotal: 'Rp. 0',
        }
    },
    methods: {
        countTotal(){
            this.total = this.total100 + this.total50 + this.total20 + this.total10 + this.total5 + this.total2 + this.total1;
            this.stringTotal = 'Rp. '+this.total.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        },
        increment100() {
            this.count100++;
            this.total100 += 100000;
            this.string100 = 'Rp. '+this.total100.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement100() {
            if(this.count100 > 0) {
                this.count100--;
            }
            this.total100 -= 100000;
            this.string100 = 'Rp. '+this.total100.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        increment50() {
            this.count50++;
            this.total50 += 50000;
            this.string50 = 'Rp. '+this.total50.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement50() {
            if(this.count50 > 0) {
                this.count50--;
            }
            this.total50 -= 50000;
            this.string50 = 'Rp. '+this.total50.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        increment20() {
            this.count20++;
            this.total20 += 20000;
            this.string20 = 'Rp. '+this.total20.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement20() {
            if(this.count20 > 0) {
                this.count20--;
            }
            this.total20 -= 20000;
            this.string20 = 'Rp. '+this.total20.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        increment10() {
            this.count10++;
            this.total10 += 10000;
            this.string10 = 'Rp. '+this.total10.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement10() {
            if(this.count10 > 0) {
                this.count10--;
            }
            this.total10 -= 10000;
            this.string10 = 'Rp. '+this.total10.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        increment5() {
            this.count5++;
            this.total5 += 5000;
            this.string5 = 'Rp. '+this.total5.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement5() {
            if(this.count5 > 0) {
                this.count5--;
            }
            this.total5 -= 5000;
            this.string5 = 'Rp. '+this.total5.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        increment2() {
            this.count2++;
            this.total2 += 2000;
            this.string2 = 'Rp. '+this.total2.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement2() {
            if(this.count2 > 0) {
                this.count2--;
            }
            this.total2 -= 2000;
            this.string2 = 'Rp. '+this.total2.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        increment1() {
            this.count1++;
            this.total1 += 1000;
            this.string1 = 'Rp. '+this.total1.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },
        decrement1() {
            if(this.count1 > 0) {
                this.count1--;
            }
            this.total1 -= 1000;
            this.string1 = 'Rp. '+this.total1.toString().replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            this.countTotal();
        },

        onSubmit(){
            const formData = new FormData();
            //formData.append("user_id", this.userId);
            formData.append("_token", this.csrf.value);
            formData.append("pec100", this.$refs.pec100.value);
            formData.append("pec50", this.$refs.pec50.value);
            formData.append("pec20", this.$refs.pec20.value);
            formData.append("pec10", this.$refs.pec10.value);
            formData.append("pec5", this.$refs.pec5.value);
            formData.append("pec2", this.$refs.pec2.value);
            formData.append("pec1", this.$refs.pec1.value);
            formData.append("nominal", this.$refs.nominal.value);
            axios.post("/deposit", formData)
            .then(response => {
                console.log('ok');
                console.log(response.data);
            })
            .catch(error => {
                //
                console.log('failed');
            });

            // this.$refs.title.value = "";
            // this.$refs.body.value = "";
        }
    }
}

</script>

<style>
    .flex-break{
        flex-basis: 100%;
        height: 0;
    }
    .deposit-form{
        padding: 3rem;
        margin-right: 5%;
        display: flex;
        flex-wrap: wrap;
        justify-content:right;
    }
    .deposit-form-container{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        border-radius: 5rem;
        width: 60vw;
        margin-top: 5rem;

        background: rgba(	0, 0, 88, 0.2);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.52);
    }
    .deposit-calculator-pecahan{
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .minus{
        fill: #FFFAF0;
        font-size: 2rem;
        padding-right: 1rem;
        padding-left: 1rem;
    }
    .plus{
        fill: #FFFAF0;
        padding-right: 1rem;
        padding-left: 1rem;
        font-size: 2rem;
    }
    .count{
        display: flex;
        justify-content: center;
        text-align: center;
        width: 3rem;
        height: 3rem;
        border-radius:5px;
        color: #FFFAF0;
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 0px solid rgba(255, 255, 255, 0.52);
        font-size: 2rem;
        font-weight: bold;
    }
    .pecahan-title{
        display: flex;
        margin-right: 4vw;
        text-align: left;
        font-size: 2rem;
        color: #FFFAF0;
    }
    .pecahan-title h2{
        margin:1rem;
    }
    .main-form-pecahan{
        display: flex;
        align-items: center;
    }
    .pecahan-total-form{
        display: flex;
    }
    .pecahan-total-string{
        display: flex;
        justify-content: center;
        margin-left: 4vw;
        text-align: center;
        width: 20rem;
        height: 3rem;
        border-radius:5px;
        color: #FFFAF0;
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        border: 0px solid rgba(255, 255, 255, 0.52);
        font-size: 2rem;
        font-weight: bold;
    }

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .button-17 {
    align-items: center;
    appearance: none;
    background-color: rgb(245, 191, 73);
    border-radius: 24px;
    border-style: none;
    box-shadow: rgba(0, 0, 0, .2) 0 3px 5px -1px,rgba(0, 0, 0, .14) 0 6px 10px 0,rgba(0, 0, 0, .12) 0 1px 18px 0;
    box-sizing: border-box;
    color: #3c4043;
    cursor: pointer;
    display: inline-flex;
    fill: currentcolor;
    font-family: "Google Sans",Roboto,Arial,sans-serif;
    font-size: 14px;
    font-weight: 500;
    height: 48px;
    justify-content: center;
    letter-spacing: .25px;
    line-height: normal;
    max-width: 100%;
    overflow: visible;
    padding: 2px 24px;
    position: relative;
    text-align: center;
    text-transform: none;
    transition: box-shadow 280ms cubic-bezier(.4, 0, .2, 1),opacity 15ms linear 30ms,transform 270ms cubic-bezier(0, 0, .2, 1) 0ms;
    user-select: none;
    -webkit-user-select: none;
    touch-action: manipulation;
    width: auto;
    will-change: transform,opacity;
    z-index: 0;
    }

    .button-17:hover {
    background: #F6F9FE;
    color: #174ea6;
    }

    .button-17:active {
    box-shadow: 0 4px 4px 0 rgb(60 64 67 / 30%), 0 8px 12px 6px rgb(60 64 67 / 15%);
    outline: none;
    }

    .button-17:focus {
    outline: none;
    border: 2px solid #4285f4;
    }

    .button-17:not(:disabled) {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
    }

    .button-17:not(:disabled):hover {
    box-shadow: rgba(60, 64, 67, .3) 0 2px 3px 0, rgba(60, 64, 67, .15) 0 6px 10px 4px;
    }

    .button-17:not(:disabled):focus {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
    }

    .button-17:not(:disabled):active {
    box-shadow: rgba(60, 64, 67, .3) 0 4px 4px 0, rgba(60, 64, 67, .15) 0 8px 12px 6px;
    }

    .button-17:disabled {
    box-shadow: rgba(60, 64, 67, .3) 0 1px 3px 0, rgba(60, 64, 67, .15) 0 4px 8px 3px;
    }
</style>
