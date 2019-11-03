<template>
  <div class="index">
    <router-view />
    <TabBar :data="tabbarData" />
  </div>
</template>

<script>
import TabBar from "../components/TabBar";
export default {
  name: "Index",
  components: {
    TabBar
  },
  data() {
    return {
      tabbarData: [
        { title: "首页", icon: "wap-home-o", path: "/home" },
        { title: "订单", icon: "orders-o", path: "/order" },
        { title: "我的", icon: "user-o", path: "/me" }
      ]
    };
  },
  created() {
    const haveopenid = localStorage.openid ? true : false;
    var havefirstlogin = localStorage.firstlogin ? true : false;
    const openidReq = this.getQueryVariable("openid");
    const firstloginReq = this.getQueryVariable("firstlogin");
    const isregisterReq = this.getQueryVariable("isregister");
    if (isregisterReq != 1) {//不是注册页面的时候才生效，注册时候用注册页面的请求
      if (!haveopenid) {//缓存中有没有openid
        //跳转回来时，带上了openid和firstlogin参数，保存到缓存
        if (openidReq) {
          localStorage.setItem("openid", openidReq);
          localStorage.setItem("firstlogin", firstloginReq);
          havefirstlogin = true;
          if (firstloginReq == 0) {
            //用openid去get全部用户信息回来
            this.$axios("https://takeawayapi.pykky.com/?s=Users.GetUserInfo", {
              params: {
                openid: openidReq
              }
            }).then(res => {
              this.$store.dispatch("setUserInfo", res.data.data);
            });
          }
        }
        if (!havefirstlogin) {//缓存中没有fistlogin
          const appid = "wx3df92dead7bcd174";
          const redirectUrl = encodeURI(
            "https://takeawayapi.pykky.com/?s=Users.GetOpenid"
          );
          const wechatUrl =
            "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
            appid +
            "&redirect_uri=" +
            redirectUrl +
            "&response_type=code&scope=snsapi_base&state=indexLogin#wechat_redirect";
          window.location.href = wechatUrl;
        }
      } else {
        //用openid去get全部用户信息回来
        const openid = localStorage.openid;
        this.$axios("https://takeawayapi.pykky.com/?s=Users.GetUserInfo", {
          params: {
            openid: openid
          }
        }).then(res => {
          this.$store.dispatch("setUserInfo", res.data.data);
          localStorage.setItem("firstlogin", res.data.data.firstlogin);
        });
      }
    } 
  },
  methods: {
    getQueryVariable(variable) {//从地址中获取参数
      var query = window.location.search.substring(1);
      var vars = query.split("&");
      for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
          return pair[1];
        }
      }
      return false;
    }
  }
};
</script>

<style lang="scss" scoped>
.index {
  width: 100%;
  height: calc(100% - 50px);
}
</style>
