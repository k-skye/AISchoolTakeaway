<template>
  <div class="index">
    <router-view></router-view>
    <TabBar :data="tabbarData" />
  </div>
</template>

<script>
import TabBar from "../components/TabBar";
export default {
  name: "index",
  data() {
    return {
      tabbarData: [
        { title: "接单", icon: "orders-o", path: "/order" },
        { title: "配送", icon: "logistics", path: "/deliver" },
        { title: "我的", icon: "user-o", path: "/me" }
      ]
    };
  },
  components: {
    TabBar
  }
};
</script>

<style lang="scss" scoped>
.index {
  width: 100%;
  height: calc(100% - 50px);
}
</style>
