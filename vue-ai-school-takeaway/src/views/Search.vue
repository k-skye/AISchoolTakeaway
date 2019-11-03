<template>
  <div class="search">
    <div class="header">
      <van-nav-bar
        :title="title"
        left-arrow
        @click-left="$router.go(-1)"
      />
    </div>
    <div class="chooseRest">
      <!-- 商家信息 -->
      <div class="shoplist">
        <IndexShop
          v-for="(item,index) in restaurants"
          :key="index"
          :restaurant="item"
        />
      </div>
    </div>
  </div>
</template>

<script>
import IndexShop from "../components/IndexShop";
export default {
  name: "Search",
  components: {
    IndexShop
  },
  data() {
    return {
      title: "第一饭堂",
      restaurants: [] // 存放所有商家容器
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.restaurants = to.params.restaurants;
      vm.title = '包含 '+to.params.title+' 的店铺';
    });
  }
};
</script>

<style lang="scss" scoped>
.search {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
</style>
