<template>
    <div :class="keyboardClass"></div>
</template>

<script>
import Keyboard from "simple-keyboard";
import "simple-keyboard/build/css/index.css";

export default {
  name: "SimpleKeyboard",
  props: {
    keyboardClass: {
      type: String
    },
    input: {
      type: String
    },
    inputName: {
      type: String
    }
  },
  data: () => ({
    keyboard: null
  }),
  mounted() {
    this.keyboard = new Keyboard(".note-keyboard",{
      onChange: this.onChange,
      onKeyPress: this.onKeyPress,
      theme: "hg-theme-default hg-layout-default",
      inputName: this.inputName,
      layout: {
        default: [
        "1 2 3 4 5 6 7 8 9 0 {bksp}",
        "q w e r t y u i o p",
        "a s d f g h j k l '",
        "z x c v b n m , . /",
        "{space}"
        ]
    },
    mergeDisplay: true,

    display: {
    '{bksp}': 'backspace',
    '{space}': 'S   P   A   C   E',
    }

    });
  },
  methods: {
    onChange(input) {
      this.$emit("onChange", input);
    },
    onKeyPress(button) {
      this.$emit("onKeyPress", button);

      /**
       * If you want to handle the shift and caps lock buttons
       */
      if (button === "{shift}" || button === "{lock}") this.handleShift();
    },
    handleShift() {
      let currentLayout = this.keyboard.options.layoutName;
      let shiftToggle = currentLayout === "default" ? "shift" : "default";

      this.keyboard.setOptions({
        layoutName: shiftToggle
      });
    }
  },
  watch: {
    inputName(inputName) {
      console.log("SimpleKeyboard: inputName updated", inputName);
      this.keyboard.setOptions({ inputName });
    },
    input(input) {
      console.log(
        "SimpleKeyboard: input Updated",
        this.keyboard.options.inputName,
        input
      );
      this.keyboard.setInput(input);
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
