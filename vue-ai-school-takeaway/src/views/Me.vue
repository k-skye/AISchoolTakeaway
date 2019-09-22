<template>
  <div class="me">
    <div class="headInfo">
      <div class="head-img"></div>
      <div class="head-profile">
        <p v-if="firstlogin" class="user-id" @click="handleRes">登陆/注册</p>
        <p class="user-id" v-else>{{userInfo.name}}</p>
        <p class="user-phone">
          <i class="fa fa-mobile"></i>
          <span v-if="firstlogin">登陆后就能开始点餐咯～</span>
          <span v-else>{{encryptPhone(userInfo.phoneNo)}}</span>
        </p>
      </div>
      <i class="fa fa-angle-right"></i>
    </div>
    <div v-if="!firstlogin">
      <div class="address-cell">
        <i class="fa fa-map-marker"></i>
        <div class="address-index" @click="myAddress">
          <span>我的地址</span>
          <i class="fa fa-angle-right"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "me",
  data() {
    return {
      firstlogin: true
    };
  },
  created() {
    if (localStorage.firstlogin == 1) {
      this.firstlogin = true;
    } else {
      this.firstlogin = false;
    }
  },
  methods: {
    handleRes() {
      const appid = "wx3df92dead7bcd174";
      const redirectUrl = encodeURI(
        "https://takeawayapi.pykky.com/?s=Users.getInfoInWechat"
      );
      const wechatUrl =
        "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
        appid +
        "&redirect_uri=" +
        redirectUrl +
        "&response_type=code&scope=snsapi_userinfo&state=register#wechat_redirect";
      window.location.href = wechatUrl;
    },
    encryptPhone(phone) {
      return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
    },
    myAddress() {
      if (this.userInfo.mainAddressID > 0) {
        this.$router.push("/myAddress");
      } else {
        this.$router.push({
          name: "addAddress",
          params: {
            title: "添加地址",
            addressInfo: {
              name: "",
              sex: "",
              phone: "",
              address: "",
              bottom: "",
              tag: ""
            }
          }
        });
      }
    }
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  }
};
</script>

<style scoped>
.me {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
}
.headInfo {
  display: flex;
  background-image: linear-gradient(90deg, #0af, #0085ff);
  padding: 6.666667vw 4vw;
  color: #fff;
  align-items: center;
}
.head-img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-position: 0px 0px;
  background-size: 60px;
  background-image: url(https://shadow.elemecdn.com/faas/h5/static/sprite.3ffb5d8.png);
}
.head-profile {
  overflow: hidden;
  margin-left: 4.8vw;
  flex-grow: 1;
}
.head-profile .user-id {
  max-width: 40vw;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  font-size: 1.3rem;
  margin-bottom: 2.133333vw;
  font-weight: 700;
  display: flex;
  align-items: center;
}
.head-profile .user-phone {
  display: flex;
  align-items: center;
  font-size: 0.8rem;
}
.user-phone > i {
  margin-right: 0.666667vw;
  font-size: 1rem;
}
.headInfo > i {
  font-size: 1.2rem;
}

.address-cell {
  margin-top: 2.666667vw;
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  font-size: 1rem;
  line-height: 4.533333vw;
  background: #ffffff;
  display: flex;
  align-items: center;
  padding-left: 6.666667vw;
  color: #333;
}
.address-cell > i {
  font-size: 1.3rem;
  color: rgb(74, 165, 240);
  margin-right: 2.666667vw;
}
.address-index {
  display: flex;
  justify-content: space-between;
  width: 100%;
  padding: 3.733333vw 2.666667vw 3.733333vw 0;
  align-content: center;
}
.address-index > i {
  font-size: 1.3rem;
  color: #ccc;
}
</style>