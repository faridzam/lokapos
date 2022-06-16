<template>
    <div class="modal-backdrop">
        <div class="modal">
            <header class="modal-header">
                <slot name="header">
                    {{itemNoteModal.name}}
                </slot>
                <button type="button" class="btn-close" @click="closeNoteModal">
                    x
                </button>
            </header>

            <section class="modal-body">
                <slot name="body">
                    <div class="form-group" v-for="k in Object.keys(inputsKeyboard)" :key="k">
                        <div class="notes-input-container">
                            <Input
                                v-model="inputsKeyboard[k]"
                                :inputs="inputsKeyboard"
                                :inputName="k"
                                @onInputFocus="onInputFocus"
                                @onInputChange="onInputChange"
                            />
                            <!-- <input type="text" class="notes-input string-input" id="note-input" v-show="k || !k && inputs.length < 1 && k == 1" v-model="input.notes"> -->
                            <div class="plus-minus" style="display:flex">
                                <span class="minus-input-icon" @click="remove(k)" v-show="k && Object.keys(inputsKeyboard).length > 1 " style="margin: 0 2rem 0 2rem">
                                    <svg-vue icon="minus-icon" style="width: 2rem; height: auto;"></svg-vue>
                                </span>
                                <span class="plus-input-icon" @click="add(k)" v-show="k && Object.keys(inputsKeyboard).length < 3" style="margin: 0 2rem 0 2rem">
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
                <button type="button" class="btn-submit" v-on:click.prevent="submitNoteModal()">
                    Add Notes
                </button>
            </footer>
        </div>
        <div class="flex-break"></div>
        <div class="note-keyboard">
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
import SimpleKeyboard from "./SimpleKeyboardNote";
import Input from "./InputNote";

export default {
    name: 'noteModal',
    data(){
        return{
            itemNoteModal: null,
            inputCount: 1,
            inputsKeyboard:{
                notes0: "",
            },
            inputName: "notes0",
        }
    },
    components: {
        SimpleKeyboard,
        Input
    },
    methods: {

        getData(item){
            //
            this.itemNoteModal = item;
            let array = item.notes;
            let arrayCount = array.length;
            let noteKeyboard = {};

            if(arrayCount > 0){
                array.forEach((value, index) => {
                    if(value){
                        noteKeyboard["notes"+index] = value;
                    } else {

                        if (index === 0) {
                            noteKeyboard["notes"+index] = "";
                        } else{
                            //
                        }
                    }
                });

                this.inputsKeyboard = noteKeyboard;
            } else {
                //

                this.inputsKeyboard = {
                    notes0: "",
                };

            }

        },

        closeNoteModal() {
            this.$emit('closeNoteModal');
        },
        submitNoteModal() {

            let item = this.itemNoteModal;
            var inputKeyArray = Object.values(this.inputsKeyboard);

            console.log(inputKeyArray);

            this.$store.commit('submitNoteModal', {
                item,
                inputKeyArray,
            });
            this.$emit('closeNoteModal');
        },

        add (index) {

            function extractSex(x) {
                //var matches = x.match(/(\d+)/);
                var number = parseInt(x)+1;
                var notesVar = "notes"+number.toString();

                return notesVar.toString();
            }

            let count = Object.keys(this.inputsKeyboard).length;
            if (this.inputsKeyboard.hasOwnProperty(extractSex(count))) {
                this.inputsKeyboard[extractSex(count+5)] = "";
                this.$forceUpdate();
            } else{
                this.inputsKeyboard[extractSex(count)] = "";
                this.$forceUpdate();
                console.log(extractSex(count));
            }

        },
        remove (index) {
            //this.inputsKeyboard.splice(index, 1)
            delete this.inputsKeyboard[index];
            this.$forceUpdate();
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
        //

    },
    watch:{
        //
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
      justify-content: flex-start;
      margin: 10px;
  }
  input{
      margin: 0 3rem 0 0;
      height: 3rem;
      width: 50%;
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
