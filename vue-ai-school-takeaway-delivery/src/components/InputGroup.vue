<template>
  <div class="text_group">
    <!-- 组件结构 -->
    <!-- 组件容器 -->
    <div
      class="input_group"
      :class="{'is-invalid':error}"
    >
      <!-- 输入框 -->
      <input
        :readonly="noinput"
        :type="type"
        :value="value"
        :placeholder="placeholder"
        :name="name"
        @input="$emit('input',$event.target.value)"
      >
      <!-- 输入框后面的按钮 -->
      <button
        v-if="btnTitle"
        :disabled="disabled"
        @click="$emit('btnClick')"
      >
        {{ btnTitle }}
      </button>
    </div>
    <!-- 错误提醒 -->
    <div
      v-if="error"
      class="invalid-feedback"
    >
      {{ error }}
    </div>
  </div>
</template>

<script>
export default {
  name: "InputGroup",
  props: {
    type: {
      type: String,
      default: "text"
    },
    value: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: ""
    },
    name: {
      type: String,
      default: ""
    },
    btnTitle: {
      type: String,
      default: ""
    },
    disabled: Boolean,
    error: {
      type: String,
      default: ""
    },
    noinput: Boolean
  }
};
</script>

<style scoped lang="scss">
.input_group {
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 10px;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  input {
    height: 100%;
    width: 100%;
    outline: none;
  }
  button {
    border: none;
    outline: none;
    width: 45%;
    background: #fff;
  }
  button[disabled] {
    color: #aaa;
  }
}
.is-invalid {
  border: 1px solid red;
}
.invalid-feedback {
  color: red;
  padding-top: 5px;
}
</style>
