<template>
  <div class="regprotocol">
    <div class="header">
      <van-nav-bar title="用户服务协议" left-arrow @click-left="$router.go(-1)" />
    </div>
    <div class="content">
      {{data}}
    </div>
  </div>
</template>

<script>
export default {
  name: "regprotocol",
  data() {
    return {
      data: ""
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getData();
    });
  },
  methods: {
    getData(){
      this.$axios(
        "https://takeawayschool.oss-cn-shenzhen.aliyuncs.com/Help.txt"
      ).then(res => {
        this.data = res.data;
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.regprotocol {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .content{
    padding: 10px;
  }
}
</style>