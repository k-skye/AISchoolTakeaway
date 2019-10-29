<template>
  <div class="comment">
    <div class="header">
      <van-nav-bar title="评价" left-arrow @click-left="$router.push('order')" />
    </div>
    <van-cell-group title=" ">
      <van-field
        v-model="message"
        rows="2"
        autosize
        
        type="textarea"
        maxlength="50"
        placeholder="请输入评价"
        show-word-limit
      />
    </van-cell-group>
    <div class="button">
      <van-button type="primary" size="large">提交</van-button>
    </div>
  </div>
</template>

<script>
export default {
  name: "comment",
  data() {
    return {
      message: ""
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getData();
    });
  },
  methods: {
    getData() {}
  }
};
</script>

<style lang="scss" scoped>
.comment {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .button{
      margin: 10px;
  }
}
</style>