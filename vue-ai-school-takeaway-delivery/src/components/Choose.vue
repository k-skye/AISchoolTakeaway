<template>
  <div class="choose">
    <van-field
      readonly
      clickable
      :label="label"
      :value="value"
      :placeholder="placeholder"
      @click="show = true"
    />

    <van-popup v-model="show" position="bottom">
      <van-picker show-toolbar :columns="data" @cancel="show = false" @confirm="onConfirm" />
    </van-popup>
  </div>
</template>

<script>
export default {
  name: "choose",
  data() {
    return {
      show: false,
      changeValue: ""
    };
  },
  props: {
    label: String,
    value: String,
    placeholder: String,
    data: Array
  },
  methods: {
    onConfirm(value) {
      this.changeValue = value;
      this.$emit("getValue",this.changeValue);
      this.show = false;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>