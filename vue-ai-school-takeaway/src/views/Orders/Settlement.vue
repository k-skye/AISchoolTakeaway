<template>
  <div class="settlement">
    <div class="header">
      <van-nav-bar title="确认订单" left-arrow @click-left="$router.push({name: 'shop',params: {restID: restInfo.id}})" />
    </div>
    <div class="view-body" v-if="orderInfo">
      <!-- 收货地址 -->
      <section class="cart-address">
        <p class="title">订单配送至</p>
        <p class="address-detail">
          <span
            @click="$router.push('/myAddress')"
            v-if="addrInfo"
          >{{addrInfo.dormitory}} {{addrInfo.roomNum}}</span>
          <span v-else>
            <span v-if="haveAddress" @click="$router.push('/myAddress')">选择收货地址</span>
            <span v-else @click="addAddress">新增收货地址</span>
          </span>
          <i class="fa fa-angle-right"></i>
        </p>
        <h2 v-if="addrInfo" class="address-name">
          <span>{{addrInfo.name}}</span>
          <span v-if="addrInfo.gender">({{addrInfo.gender == 1 ? '先生' : '小姐'}})</span>
          <span class="phone">{{addrInfo.phone}}</span>
        </h2>
      </section>

      <!-- 送达时间 -->
      <Delivery :restInfo="restInfo" />

      <!-- 点餐内容 -->
      <section class="checkout-section cart-group">
        <h3>{{restInfo.name}}</h3>
        <ul>
          <li v-for="(food,index) in orderInfo.selectFoods" :key="index">
            <img :src="'https://takeaway.pykky.com/goodImgs/'+food.logo" alt />
            <div class="cart-group-info">
              <span>{{food.name}}</span>
              <span>x {{food.count}}</span>
              <span>¥{{food.price}}</span>
            </div>
          </li>
          <li class="cart-group-deliveryFee" @click="onDeliverClick">
            <div class="deliverTitle">
              伙伴费
              <van-icon name="info-o" />
            </div>
            <div>¥{{restInfo.deliveryFee}}</div>
          </li>
          <li class="cart-group-discount" @click="showList = true">
            <van-coupon-cell
              title="红包"
              :coupons="coupons"
              :chosen-coupon="chosenCoupon"
              @click="showList = true"
            />
          </li>
          <li class="cart-group-total">
            <div class="discounts"></div>
            <div class="subtotal">
              <span class="discot">
                已优惠
                <span class="red">¥{{(orderInfo.discount.value/100)}}</span>
              </span>
              <span>小计 ¥</span>
              <span class="price">{{realPrice}}</span>
            </div>
          </li>
        </ul>
      </section>

      <!-- 备注信息 -->
      <section class="checkout-section">
        <CartItem
          @click="showTableware=true"
          title="餐具份数"
          :subHead="remarkInfo.tableware || '未选择'"
        />
        <CartItem
          @click="$router.push('/remark')"
          title="订单备注"
          :subHead="remarkInfo.remark || '口味 偏好'"
        />
      </section>

      <!-- 显示Tableware -->
      <Tableware :isShow="showTableware" @close="showTableware=false" />
      <!-- 优惠券列表 -->
      <van-popup v-model="showList" position="bottom">
        <van-coupon-list
          :show-exchange-bar="false"
          :coupons="coupons"
          :chosen-coupon="chosenCoupon"
          :disabled-coupons="disabledCoupons"
          @change="onChange"
        />
      </van-popup>
    </div>

    <!-- 底部 -->
    <footer class="action-bar">
      <span>¥{{realPrice}}</span>
      <button @click="handlePay">微信支付</button>
    </footer>
  </div>
</template>

<script>
import Delivery from "../../components/Orders/Delivery";
import CartItem from "../../components/Orders/CartItem";
import Tableware from "../../components/Orders/Tableware";
import { Toast } from "vant";
import { Dialog } from "vant";
export default {
  name: "Settlement",
  data() {
    return {
      haveAddress: false,
      showTableware: false,
      addrInfo: null,
      chosenCoupon: -1,
      showList: false,
      coupons: [],
      disabledCoupons: []
    };
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    },
    restInfo() {
      return this.$store.getters.restInfo;
    },
    orderInfo() {
      return this.$store.getters.orderInfo;
    },
    totalPrice() {
      return this.$store.getters.totalPrice;
    },
    realPrice() {
      return this.$store.getters.realPrice;
    },
    remarkInfo() {
      return this.$store.getters.remarkInfo;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getData();
    });
  },
  methods: {
    addAddress() {
      this.$router.push({
        name: "addAddress",
        params: {
          title: "添加地址",
          userInfo: this.userInfo,
          addressInfo: {
            name: "",
            sex: "",
            phone: "",
            address: "",
            bottom: ""
          }
        }
      });
    },
    onChange(index) {
      this.showList = false;
      this.chosenCoupon = index;
      if (index == -1) {
        this.$store.dispatch("setDiscount", { id: null, value: null });
        return;
      }
      const coups = {
        id: this.coupons[index].id,
        value: this.coupons[index].value
      };
      this.$store.dispatch("setDiscount", coups);
    },
    onDeliverClick() {
      Dialog({
        message:
          "伙伴费：平台的伙伴们帮忙配送、跑腿时的服务费，是伙伴们的主要收入"
      });
    },
    getData() {
      //拿收货地址
      this.addrInfo = this.$store.getters.addrInfo;
      if (this.userInfo.mainAddressID != 0) {
        this.haveAddress = true;
        if (!this.addrInfo) {
          this.$axios("https://takeawayapi.pykky.com/?s=Address.GetOneAddr", {
            params: {
              id: this.userInfo.mainAddressID
            }
          }).then(res => {
            this.addrInfo = res.data.data;
            this.$store.dispatch("setAddrInfo", this.addrInfo);
          });
        }
      } else {
        this.haveAddress = false;
      }
      //拿用户红包
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Discount.GetOnesAllcounts",
        {
          params: {
            userID: this.userInfo.id
          }
        }
      ).then(res => {
        res.data.data.forEach(element => {
          element.forEach(item => {
            item.condition = item.conditions;
          });
        });
        this.disabledCoupons = res.data.data[0];
        this.coupons = res.data.data[1];
      });
    },
    handlePay() {
      if (!this.addrInfo) {
        Toast("请先选择收货地址");
        return;
      }
      //开始创建订单
      var foodArrID = []; //食物的id数组
      this.orderInfo.selectFoods.forEach(element => {
        //让2份以上的商品也添加上，不然无论你点了多少分都只记录一份
        if (element.count > 1) {
          for (let i = 0; i < element.count; i++) {
            foodArrID.push(Number(element.id));
          }
        }else{
          //只有一份
          foodArrID.push(Number(element.id));
        }
      });
      foodArrID = JSON.stringify(foodArrID).toString();
      var remarks = this.remarkInfo.tableware + "," + this.remarkInfo.remark;
      if (remarks == ",") {
        remarks = "无";
      }
      var discID = -1;
      if (!this.orderInfo.discount.id) {
        discID = -1;
      } else {
        discID = this.orderInfo.discount.id;
      }
      this.$axios
        .post("https://takeawayapi.pykky.com/?s=Orders.CreateOneOrder", {
          userID: this.userInfo.id,
          foodArrID: foodArrID,
          remark: remarks,
          restID: this.restInfo.id,
          totalPrice: this.totalPrice,
          payPrice: this.realPrice,
          addrID: this.addrInfo.id,
          discountID: discID,
          openID: this.userInfo.openid
        })
        .then(res => {
          //微信支付
          const data = JSON.parse(res.data.data);
          WeixinJSBridge.invoke("getBrandWCPayRequest", data, res => {
            if (res.err_msg == "get_brand_wcpay_request:ok") {
              // 使用以上方式判断前端返回,微信团队郑重提示：
              //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
              Dialog({
                message: "支付成功"
              });
              this.$router.push("/order");
            } else {
              Dialog({
                message:
                  "支付失败：" +
                  res.data.data.err_code +
                  res.data.data.err_desc +
                  res.data.data.err_msg
              });
            }
          });
        });
    }
  },
  components: {
    Delivery,
    CartItem,
    Tableware
  }
};
</script>

<style lang="scss" scoped>
.settlement {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  overflow: auto;
  .view-body {
    width: 100%;
    box-sizing: border-box;
    font-size: 0.9rem;
    color: #333;
    padding-bottom: 14.133333vw;
    padding-left: 1.6vw;
    padding-right: 1.6vw;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    .cart-address {
      background-color: transparent;
      padding: 4.266667vw 2.133333vw 2.933333vw 2.133333vw;
      min-height: 22.133333vw;
      color: black;
      overflow: hidden;
      background-color: white;
      margin-top: 10px;
      padding-left: 5.133333vw;
      .title {
        font-size: 0.9rem;
        line-height: 1.21;
        color: gray;
      }
      .address-detail {
        font-size: 1.3rem;
        font-weight: 600;
        line-height: 1.88;
        overflow: hidden;
        display: flex;
        align-items: center;
      }
      span {
        display: inline-block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: calc(100% - 4vw);
      }
      i {
        margin-left: 2.133333vw;
      }
      .address-name {
        font-size: 0.86rem;
        line-height: 1.21;
        margin-bottom: 1.333333vw;
        .phone {
          margin-left: 2.133333vw;
        }
      }
    }
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
  } /* 底部支付样式 */
  .action-bar {
    height: 11.733333vw;
    position: fixed;
    left: 0;
    bottom: 0;
    width: 100%;
    background: #3c3c3c;
    z-index: 2;
    span {
      color: #fff;
      font-size: 1.2rem;
      line-height: 11.733333vw;
      padding-left: 2.666667vw;
      vertical-align: middle;
    }
    button {
      position: absolute;
      top: 0;
      right: 0;
      bottom: 0;
      background: #00e067;
      min-width: 28vw;
      border: none;
      outline: none;
      color: #fff;
      font-size: 1.2rem;
      height: 100%;
    }
  }
}
</style>
