<template>
  <div class="help">
    <div class="header">
      <van-nav-bar title="帮助" left-arrow @click-left="$router.push('me')" />
    </div>
    <van-collapse v-model="activeName" accordion>
      <van-collapse-item
        v-for="(help,index) in helpList"
        :key="index"
        :title="help.question"
        :name="index"
      >{{help.answer}}</van-collapse-item>
    </van-collapse>
  </div>
</template>

<script>
export default {
  name: "help",
  data() {
    return {
      activeName: "0",
      helpList: null
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getData();
    });
  },
  methods: {
    getData() {
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Help.getUserAllHelp"
      ).then(res => {
        /* if (JSON.stringify(res.data.data) == "{}") {
          return;
        } */
        this.helpList = res.data.data;
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.help {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
</style>