<template>
  <div class="tabbar">
    <van-tabbar
      route
      safe-area-inset-bottom
    >
      <van-tabbar-item
        v-for="(item,index) in data"
        :key="index"
        replace
        :to="item.path"
        :icon="item.icon"
      >
        {{ item.title }}
      </van-tabbar-item>
    </van-tabbar>
  </div>
</template>

<script>
export default {
  name: "Tabbar",
  props: {
    data: {
      type: Array,
      default: () => []
    }
  }
};
</script>

<style lang="scss" scoped>
.tabbar{
  height: 50px;
}
</style>