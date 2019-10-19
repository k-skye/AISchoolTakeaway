<template>
  <div class="shop" v-if="shopInfo">
    <!-- 头部 -->
    <nav class="header-nav">
<!--       <div class="nav_bg">
        <img :src="'https://takeaway.pykky.com/restImgs/'+shopInfo.logo" alt />
      </div> -->
      <div class="nav_back">
        <i @click="$router.push('/home')" class="fa fa-chevron-left"></i>
      </div>
<!--       <div class="shop_image">
        <img :src="'https://takeaway.pykky.com/restImgs/'+shopInfo.logo" alt />
      </div> -->
    </nav>

    <!-- 商家信息 -->
    <div class="index-rst">
      <div class="rst-name">
        <span @click="showInfoModel = true">
          {{shopInfo.name}}
          <i class="fa fa-caret-right"></i>
        </span>
      </div>
      <!-- 弹窗信息 -->
      <InfoModel
        @close="showInfoModel = false"
        :shopInfo="shopInfo"
        :showInfoModel="showInfoModel"
      />

<!--       <!-- 评分月售
      <div class="rst-order">
        <span>评分{{shopInfo.stars}}</span>
        <span>月售{{shopInfo.salesNum}}单</span>
        <span>蜂鸟专送约13分钟</span>
      </div> -->

      <!-- 公告 -->
      <p class="rst-promotion">公告: {{shopInfo.note}}</p>
    </div>

    <!-- 导航 -->
    <NavBar />
    <keep-alive>
      <router-view></router-view>
    </keep-alive>
  </div>
</template>

<script>
import InfoModel from "../../components/Shops/InfoModel";
import NavBar from "../../components/Shops/NavBar";

export default {
  name: "Shop",
  data() {
    return {
      restID: "",
      shopInfo: null,
      showInfoModel: false
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.restID = to.params.restID;
      vm.getData();
      vm.$router.push('goods');
    });
  },
  methods: {
    getData() {
      this.$axios("https://takeawayapi.pykky.com/?s=Restaurant.GetOneRest", {
        params: {
          id: this.restID
        }
      }).then(res => {
        this.shopInfo = res.data.data;
        this.$store.dispatch("setRestInfo", this.shopInfo);
      });
    }
  },
  components: {
    InfoModel,
    NavBar
  },
};
</script>

<style scoped>
.shop {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
.header-nav {
  position: relative;
}
.nav_bg img {
  width: 100%;
  height: 26.666667vw;
}
.nav_back {
  position: absolute;
  top: 0;
  left: 0;
  /* width: 100%; */
  height: 26.666667vw;
  /* background: rgba(0, 0, 0, 0.5); */
}
.nav_back i {
  color: #323233;
  font-size: 1.3rem;
  margin-left: 1.333333vw;
  margin-top: 1.333333vw;
  padding-top: 1vw;
}
.shop_image {
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -10vw;
  margin-top: 11vw;
}
.shop_image img {
  width: 20vw;
  height: 20vw;
  border-radius: 0.8vw;
}

.index-rst {
  padding: 1vw 0 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: #fff;
  box-shadow: inset 0 -0.666667vw 0.666667vw hsla;
}
.index-rst .rst-name {
  flex: 1;
  width: 72vw;
  font-size: 1.3rem;
  font-weight: 700;
  white-space: nowrap;
  padding-right: 2.666667vw;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 1.6vw 0;
}
.rst-name span {
  text-align: left;
  overflow: hidden;
  text-overflow: ellipsis;
}

.index-rst .rst-order {
  white-space: nowrap;
  height: 3.2vw;
  margin-top: 1.733333vw;
  color: #666;
  text-align: center;
  font-size: 0.8rem;
}
.rst-order span {
  margin: 0 3px;
}
.index-rst .rst-promotion {
  width: 80vw;
  font-size: 0.7rem;
  color: #999;
  overflow: hidden;
  text-overflow: ellipsis;
  margin: 2.266667vw auto 2.666667vw;
  padding: 0;
  white-space: nowrap;
}
</style>