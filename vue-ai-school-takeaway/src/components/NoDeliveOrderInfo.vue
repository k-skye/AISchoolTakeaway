<template>
  <div class="nodeliveorderinfo">
    <div class="nologin">
      <div class="content">
      <van-icon name="shopping-cart-o" size="50" color="gray"/>
      <span>您没有订单噢，先去下单吧！</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "nodeliveorderinfo"
};
</script>

<style lang="scss" scoped>
.nodeliveorderinfo{
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