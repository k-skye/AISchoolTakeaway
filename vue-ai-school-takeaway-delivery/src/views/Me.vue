<template>
  <div class="me">
    <div class="header">
      <div class="headImg"></div>
      <div v-if="firstlogin" class="reg">
        马上注册成为伙伴吧～
        <van-icon name="arrow" />
      </div>
      <div v-else class="info">
        无限大人
        <br />18237817281
        <!-- <van-icon name="arrow" /> -->
      </div>
    </div>
    <div class="contain">
      <van-cell-group title=" ">
        <van-cell icon="paid" title="钱包" is-link value="¥99.0" to="wallet"/>
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell icon="orders-o" is-link @click="$router.push('allorder')" >
          <template slot="title">
            <span class="custom-title">订单</span>
            <van-tag round type="success">37</van-tag>
          </template>
        </van-cell>
        <van-cell icon="star-o" is-link @click="$router.push('comment')">
          <template slot="title">
            <span class="custom-title">评价</span>
            <van-tag round type="warning">23</van-tag>
          </template>
        </van-cell>
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell icon="question-o" @click="$router.push('help')" title="帮助" is-link />
        <van-cell icon="notes-o" @click="$router.push('protocol')" title="协议" is-link />
        <van-cell icon="bullhorn-o" @click="$router.push('support')" title="建议反馈" is-link />
      </van-cell-group>
    </div>
  </div>
</template>

<script>
export default {
  name: "me",
  data() {
    return {
      firstlogin: false
    };
  },
  methods: {}
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
    .headImg {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background-position: 0px 0px;
      background-size: 60px;
      background-image: url(https://shadow.elemecdn.com/faas/h5/static/sprite.3ffb5d8.png);
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
      margin-left: 18px;
      line-height: 22px;
      font-size: 20px;
    }
  }
  .contain {
    span {
      margin-right: 10px;
    }
  }
}
</style>