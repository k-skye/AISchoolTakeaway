<template>
  <div class="protocol">
    <div class="header">
      <van-nav-bar
        title="协议"
        left-arrow
        @click-left="$router.push('me')"
      />
    </div>
    <div class="content">
      {{ data }}
    </div>
  </div>
</template>

<script>
export default {
  name: "Protocol",
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
.protocol {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .content{
    padding: 10px;
  }
}
</style>