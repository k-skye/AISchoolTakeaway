<template>
  <div class="support">
    <div class="header">
      <van-nav-bar title="建议反馈" left-arrow @click-left="$router.push('me')" />
    </div>
    <van-cell-group title=" " >
      <van-field
        v-model="message"
        label="留言"
        type="textarea"
        placeholder="请输入留言"
        rows="1"
        autosize
      >
      <van-button slot="button" size="small" type="primary" @click="onSendButtonClick">留言</van-button>
      </van-field>
    </van-cell-group>
  </div>
</template>

<script>
import { Toast } from "vant";
export default {
  name: "support",
  data() {
    return {
      message: ""
    };
  },
  methods:{
    onSendButtonClick(){
      Toast.success("反馈成功");
      this.$router.push('me');
    }
  }
};
</script>

<style lang="scss" scoped>
.support {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
</style>