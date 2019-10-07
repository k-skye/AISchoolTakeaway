<template>
  <div class="nologininfo">
    <div class="nologin" @click="handleRes">
      <div class="content">
      <van-icon name="smile-o" size="50" color="gray"/>
      <span>您还未注册呢～赶紧点击注册吧</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "nologininfo",
  methods: {
      handleRes(){
      const appid = "wx3df92dead7bcd174";
      const redirectUrl = encodeURI(
        "https://takeawayapi.pykky.com/?s=DeliverUsers.getInfoInWechat"
      );
      const wechatUrl =
        "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
        appid +
        "&redirect_uri=" +
        redirectUrl +
        "&response_type=code&scope=snsapi_userinfo&state=register#wechat_redirect";
      window.location.href = wechatUrl;
    }
  }
};
</script>

<style lang="scss" scoped>
.nologininfo{
  .nologin{
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    .content{
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      .van-icon{
        display: flex;
        justify-content: center;
        width: 100vw;
        padding-bottom: 15px;
      }
      span{
        color: #323233;
      }
    }
  }
}
</style>