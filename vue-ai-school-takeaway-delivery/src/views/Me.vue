<template>
  <div class="me">
    <div class="header">
      <div class="head-img">
        <img :src="logoImgUrl" />
      </div>
      <div v-if="firstlogin" class="reg" @click="handleRes">
        点击即可注册成为伙伴
        <van-icon name="arrow" />
      </div>
      <div v-else class="info">
        {{userInfo.name}}
        <br /><div class="phone"><van-icon name="phone" />{{userInfo.phoneNo}}</div>
        <!-- <van-icon name="arrow" /> -->
      </div>
    </div>
    <div class="contain" v-if="!firstlogin">
      <van-cell-group title=" ">
        <van-cell icon="paid" title="钱包" is-link :value="'¥'+(parseFloat(userInfo.noun)+userInfo.notDoneMoney)" to="wallet" />
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell icon="orders-o" is-link @click="$router.push('allorder')">
          <template slot="title">
            <span class="custom-title">订单</span>
            <van-tag round type="success">{{userInfo.orderCount}}</van-tag>
          </template>
        </van-cell>
        <van-cell icon="star-o" is-link @click="$router.push('comment')">
          <template slot="title">
            <span class="custom-title">评价</span>
            <van-tag round type="warning">{{userInfo.commentCount}}</van-tag>
          </template>
        </van-cell>
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell icon="question-o" @click="$router.push('help')" title="帮助" is-link />
        <van-cell icon="notes-o" @click="$router.push('protocol')" title="协议" is-link />
        <van-cell icon="bullhorn-o" @click="$router.push('support')" title="建议反馈" is-link />
      </van-cell-group>
    </div>
    <div class="nologin" v-else>
      <NoLoginInfo />
    </div>
  </div>
</template>

<script>
import NoLoginInfo from "../components/NoLoginInfo";

export default {
  name: "me",
  data() {
    return {
      firstlogin: false,
      logoImgUrl: ""
    };
  },
  created() {
    this.firstlogin = localStorage.firstlogin == 0 ? false : true;
    this.logoImgUrl = this.firstlogin == false ? this.userInfo.avatar : "https://shadow.elemecdn.com/faas/h5/static/sprite.3ffb5d8.png";
  },
  methods: {
    
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  components: {
    NoLoginInfo
  }
};
</script>

<style lang="scss" scoped>
.me {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .header {
    display: flex;
    background-image: linear-gradient(90deg, #0af, #0085ff);
    padding: 6.666667vw 4vw;
    color: #fff;
    align-items: center;
    .head-img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
      }
    }
    .reg {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-grow: 1;
      margin-left: 18px;
      font-size: 23px;
    }
    .info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-grow: 1;
      flex-wrap: wrap;
      margin-left: 18px;
      line-height: 24px;
      font-size: 20px;
      .phone{
        display: flex;
        width: 100%;
        align-items: center;
      }
    }
  }
  .contain {
    span {
      margin-right: 10px;
    }
  }
  .nologin {
    height: calc(100% - 113.31px);
    display: flex;
    align-items: center;
  }
}
</style>