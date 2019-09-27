<template>
  <div class="help">
    <div class="header">
      <van-nav-bar title="帮助" left-arrow @click-left="$router.push('me')" />
    </div>
    <van-collapse v-model="activeName" accordion>
      <van-collapse-item title="标题1" name="1">内容</van-collapse-item>
      <van-collapse-item title="标题2" name="2">内容</van-collapse-item>
      <van-collapse-item title="标题3" name="3">内容</van-collapse-item>
    </van-collapse>
  </div>
</template>

<script>
export default {
  name: "help",
  data() {
    return {
      activeName: "1"
    };
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