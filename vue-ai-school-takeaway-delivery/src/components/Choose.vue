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

    <van-popup
      v-model="show"
      position="bottom"
    >
      <van-picker
        show-toolbar
        :columns="data"
        @cancel="show = false"
        @confirm="onConfirm"
      />
    </van-popup>
  </div>
</template>

<script>
export default {
  name: "Choose",
  props: {
    label: {
      type: String,
      default: ""
    },
    value: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: ""
    },
    data: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      show: false,
      changeValue: ""
    };
  },
  methods: {
    onConfirm(value, index) {
      this.changeValue = value;
      this.$emit("getValue", this.changeValue, index);
      this.show = false;
    }
  }
};
</script>

<style lang="scss" scoped>
</style>