<template>
  <div class="tabbar">
    <van-tabbar route safe-area-inset-bottom>
      <van-tabbar-item
        replace
        safe-area-inset-bottom
        v-for="(item,index) in data"
        :key="index"
        :to="item.path"
        :icon="item.icon"
      >{{item.title}}</van-tabbar-item>
    </van-tabbar>
  </div>
</template>

<script>
export default {
  name: "tabbar",
  props: {
    data: Array
  }
};
</script>

<style lang="scss" scoped>
.tabbar{
  height: 50px;
  width: 100%;
}
</style>