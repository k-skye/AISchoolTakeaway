<template>
  <div class="me">
    <div class="header">
      <div class="head-img">
        <img :src="logoImgUrl">
      </div>
      <div
        v-if="firstlogin"
        class="reg"
        @click="handleRes"
      >
        点击注册
        <van-icon name="arrow" />
      </div>
      <div
        v-else
        class="info"
      >
        {{ userInfo.name }}
        <br>
        <div class="phone">
          <van-icon name="phone" />
          {{ userInfo.phoneNo }}
        </div>
        <!-- <van-icon name="arrow" /> -->
      </div>
    </div>
    <div
      v-if="!firstlogin"
      class="contain"
    >
      <van-cell-group title=" ">
        <van-cell
          icon="paid"
          title="钱包"
          is-link
          :value="'¥'+((parseFloat(userInfo.noun)+userInfo.notDoneMoney).toFixed(2))"
          to="wallet"
        />
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell
          icon="orders-o"
          is-link
          @click="$router.push({ name: 'allorder', params: { deliverID: userInfo.id }})"
        >
          <template slot="title">
            <span class="custom-title">订单</span>
            <van-tag
              round
              type="success"
            >
              {{ userInfo.orderCount }}
            </van-tag>
          </template>
        </van-cell>
        <van-cell
          icon="star-o"
          is-link
          @click="$router.push({ name: 'comment', params: { deliverID: userInfo.id }})"
        >
          <template slot="title">
            <span class="custom-title">评价</span>
            <van-tag
              round
              type="warning"
            >
              {{ userInfo.commentCount }}
            </van-tag>
          </template>
        </van-cell>
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell
          icon="question-o"
          to="help"
          title="帮助"
          is-link
        />
        <van-cell
          icon="notes-o"
          to="protocol"
          title="协议"
          is-link
        />
        <van-cell
          icon="bullhorn-o"
          title="建议反馈"
          is-link
          @click="$router.push({name:'support',params: { deliverID: userInfo.id }})"
        />
        <van-cell
          icon="service-o"
          title="联系客服"
          is-link
          @click="serviceClick"
        />
      </van-cell-group>
    </div>
    <div
      v-else
      class="nologin"
    >
      <NoLoginInfo />
    </div>
  </div>
</template>

<script>
import NoLoginInfo from "../components/NoLoginInfo";
import { Dialog } from 'vant';
export default {
  name: "Me",
  components: {
    NoLoginInfo
  },
  data() {
    return {
      firstlogin: false,
      logoImgUrl: ""
    };
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  created() {
    this.firstlogin = localStorage.firstlogin == 0 ? false : true;
    this.logoImgUrl =
      this.firstlogin == false
        ? this.userInfo.avatar
        : "http://takeawaydeliver.pykky.com/newICON.png";
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getuserInfo();
    });
  },
  methods: {
    serviceClick(){
      Dialog({ message: '请加平台客服微信：17889465893，如紧急可拨打此电话！' });
    },
    handleRes(){
      const appid = "wx3df92dead7bcd174";
      const redirectUrl = encodeURI(
        "http://tatestapi.pykky.com/?s=DeliverUsers.getInfoInWechat"
      );
      const wechatUrl =
        "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
        appid +
        "&redirect_uri=" +
        redirectUrl +
        "&response_type=code&scope=snsapi_userinfo&state=register#wechat_redirect";
      window.location.href = wechatUrl;
    },
    getuserInfo() {
      const openid = localStorage.openid;
      //用openid去get全部用户信息回来
      this.$axios("http://tatestapi.pykky.com/?s=DeliverUsers.GetUserInfo", {
        params: {
          openid: openid
        }
      }).then(res => {
        this.$store.dispatch("setUserInfo", res.data.data);
      });
    }
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
      .phone {
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