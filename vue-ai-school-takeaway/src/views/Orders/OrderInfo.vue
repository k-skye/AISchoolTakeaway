<template>
  <div class="orderInfo">
    <div class="header">
      <van-nav-bar title="订单详情" left-arrow @click-left="$router.push('order')" />
    </div>
    <div class="view-body" v-if="orderDetail">
      <div class="status-head">
        <div class="status-text">订单已送达</div>
        <div class="status-title">感谢您对我们外卖服务的信任, 期待再次光临</div>
      </div>
      <div class="restaurant-card">
        <!-- 点餐内容 -->
        <section class="checkout-section cart-group">
          <h3>{{orderDetail.restName}}</h3>
          <ul v-if="orderDetail.selectFoods">
            <li v-for="(food,index) in orderDetail.selectFoods" :key="index">
              <img :src="food.logo" alt />
              <div class="cart-group-info">
                <span>{{food.name}}</span>
                <span>¥{{food.price}}</span>
              </div>
            </li>
            <li class="cart-group-deliveryFee">
              <div class="deliverTitle">伙伴费</div>
              <div>¥{{orderDetail.deliveryFee}}</div>
            </li>
            <li class="cart-group-discount">
              <div class="discountTitle">红包</div>
              <div>- ¥{{(orderDetail.value/100)}}</div>
            </li>
            <li class="cart-group-total">
              <div class="discounts"></div>
              <div class="subtotal">
                <span class="discot">
                  已优惠
                  <span class="red">¥{{(orderDetail.value/100)}}</span>
                </span>
                <span>小计 ¥</span>
                <span class="price">{{orderDetail.payPrice}}</span>
              </div>
            </li>
          </ul>
        </section>
      </div>

      <!-- 订单信息 -->
      <div class="detail-card" v-if="orderDetail.remark">
        <div class="title">订单信息</div>
        <ul class="card-list">
          <li class="list-item">
            <span>下单时间: {{orderDetail.createTime}}</span>
          </li>
          <li class="list-item">
            <span>订单备注: {{orderDetail.remark}}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "OrderInfo",
  data() {
    return {
      orderDetail: {},
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.orderDetail = to.params;
    });
  },
};
</script>

<style lang="scss" scoped>
.orderInfo {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  overflow: auto;
  .view-body {
    .status-head {
      margin: 2.666667vw;
      box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
      background-color: #fff;
      padding: 0 3.2vw 5.333333vw;
      .status-text {
        margin: 0 auto 4.266667vw;
        color: #333;
        font-size: 1.5rem;
        text-align: left;
        padding: 4.266667vw 0;
        border-bottom: 0.133333vw solid #ddd;
      }
      .status-title {
        font-size: 1rem;
        color: #333;
        margin-bottom: 1.866667vw;
      }
    }
    .restaurant-card {
      margin: 2.666667vw;
      box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
      background-color: #fff;
      .checkout-section {
        margin-bottom: 2.133333vw;
        padding: 0 5.333333vw;
        background: #fff;
        box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
      }
      .checkout-section {
        margin-bottom: 2.133333vw;
        padding: 0 5.333333vw;
        background: #fff;
        box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
      }
      .cart-group > h3 {
        padding: 4.266667vw 0;
        color: #333;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        font-size: 1.1rem;
        line-height: 1;
        font-weight: 700;
      }
      .cart-group > ul {
        margin-top: 0.666667vw;
        color: #333;
        font-size: 0.9rem;
      }
      .cart-group > ul > li {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 3.2vw 0;
        color: inherit;
      }
      .cart-group > ul > li > img {
        width: 9.6vw;
        height: 9.6vw;
      }
      .cart-group-info {
        margin-left: 2.133333vw;
        overflow: hidden;
        flex: 9;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }
      .cart-group-deliveryFee {
        justify-content: space-between;
        padding: 4vw 0 1vw !important;
        border-top: 1px dotted #eee;
        .deliverTitle {
          display: flex;
        }
      }
      .cart-group-discount {
        justify-content: space-between;
      }
      .van-coupon-cell {
        justify-content: space-between;
        padding: 1vw 0 4vw !important;
        border-bottom: 1px dotted #eee;
      }
      .cart-title {
        color: red;
      }
      .cart-group-total {
        justify-content: space-between;
        padding: 4vw 0 4.8vw !important;
        border-bottom: 1px dotted #eee;
      }
      .discounts {
        color: #bbb;
      }
      .subtotal {
        .discot {
          margin-right: 30px;
          .red {
            color: red;
          }
        }
        .price {
          font-size: 1.5rem;
          font-weight: 500;
        }
      }
    }
    /* 配送和订单 */
    .detail-card {
      margin: 2.666667vw;
      box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
      background-color: #fff;
      padding: 0 3.2vw 5.333333vw;
      .title {
        font-size: 1rem;
        color: #333;
        line-height: 10.4vw;
      }
      .card-list {
        .list-item {
          color: #6e6e6e;
          border-top: 1px solid #f2f2f2;
          display: flex;
          align-items: baseline;
          line-height: 4.8vw;
          font-size: 0.88rem;
          padding: 3.2vw 8vw 2.666667vw 0;
          .content {
            line-height: 1.5em;
            padding-bottom: 2.666667vw;
            display: flex;
            flex-direction: column;
            flex: 1;
          }
          span:first-child {
            flex: none;
          }
        }
      }
    }
  }
}
</style>

