<template>
  <input
    :id="inputName"
    :value="inputValue"
    :placeholder="inputName"
    @focus="onInputFocus"
    @input="onInputChange"
  >
</template>

<script>
export default {
  name: "Input",
  props: {
    inputs: {
      type: Object,
    },
    inputName: {
      type: String,
    }
  },
  methods: {
    onInputChange(input) {
      this.$emit("onInputChange", input);
    },
    onInputFocus(input) {
      this.$emit("onInputFocus", input);
    }
  },
  computed: {
    inputValue: function () {
      return this.inputs[this.inputName];
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
    input{
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
</style>
