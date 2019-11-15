<template>
  <div class="tabbar">
    <van-tabbar
      route
      safe-area-inset-bottom
      style="z-index:999"
    >
      <van-tabbar-item
        v-for="(item,index) in data"
        :key="index"
        replace
        safe-area-inset-bottom
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
.tabbar {
  /* height: 50px; */
  width: 100%;
}
</style>