<template>
  <div class="settlement">
    <div class="header">
      <van-nav-bar
        title="确认订单"
        left-arrow
        @click-left="$router.push({name: 'shop',params: {restID: restInfo.id}})"
      />
      <!-- @click-left="$router.push({name: 'shop',params: {restID: restInfo.id}})" -->
    </div>
    <van-popup
      v-model="showPicker"
      position="bottom"
    >
      <van-picker
        show-toolbar
        :columns="columns"
        @cancel="showPicker = false"
        @confirm="onConfirm"
      />
    </van-popup>
    <div
      v-if="orderInfo"
      class="view-body"
    >
      <!-- 收货地址 -->
      <section class="cart-address">
        <p class="title">
          订单配送至
        </p>
        <p class="address-detail">
          <span
            v-if="addrInfo"
            @click="$router.push('/myAddress')"
          >{{ addrInfo.dormitory }} {{ addrInfo.roomNum }}</span>
          <span v-else>
            <span
              v-if="haveAddress"
              @click="$router.push('/myAddress')"
            >选择收货地址</span>
            <span
              v-else
              @click="addAddress"
            >新增收货地址</span>
          </span>
          <i class="fa fa-angle-right" />
        </p>
        <h2
          v-if="addrInfo"
          class="address-name"
        >
          <span>{{ addrInfo.name }}</span>
          <span v-if="addrInfo.gender">({{ addrInfo.gender == 1 ? '先生' : '小姐' }})</span>
          <span class="phone">{{ addrInfo.phone }}</span>
        </h2>
      </section>

      <!-- 送达时间 -->
      <section
        class="checkout-section"
        @click="showPicker = true"
      >
        <div class="delivery">
          <div class="deliver-left">
            <span class="delivery-time">送达时间</span>
          </div>
          <div class="delivery-right">
            <span class="delivery-select">{{ okTime }}</span>
            <i class="fa fa-angle-right" />
          </div>
        </div>
      </section>

      <!-- 点餐内容 -->
      <section class="checkout-section cart-group">
        <h3>{{ restInfo.name }}</h3>
        <ul>
          <li
            v-for="(food,index) in orderInfo.selectFoods"
            :key="index"
          >
            <img
              :src="'https://takeawayschool.oss-cn-shenzhen.aliyuncs.com/goodImgs/'+food.logo"
              alt
            >
            <div class="cart-group-info">
              <span>{{ food.name }}</span>
              <span>x {{ food.count }}</span>
              <span>¥{{ parseFloat((food.price)).toFixed(2) }}</span>
            </div>
          </li>
          <li
            class="cart-group-deliveryFee"
            @click="onBoxClick"
          >
            <div class="deliverTitle">
              包装费
              <van-icon name="info-o" />
            </div>
            <div>¥{{ packgeFee }}</div>
          </li>
          <li
            class="cart-group-deliveryFee"
            @click="onDeliverClick"
          >
            <div class="deliverTitle">
              伙伴费
              <van-icon name="info-o" />
            </div>
            <div>¥{{ okFee }}</div>
          </li>
          <li
            class="cart-group-discount"
            @click="showList = true"
          >
            <van-coupon-cell
              title="红包"
              :coupons="coupons"
              :chosen-coupon="chosenCoupon"
              @click="showList = true"
            />
          </li>
          <li class="cart-group-total">
            <div class="discounts" />
            <div class="subtotal">
              <span class="discot">
                已优惠
                <span class="red">¥{{ (orderInfo.discount.value/100).toFixed(2) }}</span>
              </span>
              <span>小计 ¥</span>
              <span class="price">{{ realPrice.toFixed(2) }}</span>
            </div>
          </li>
        </ul>
      </section>

      <!-- 备注信息 -->
      <section class="checkout-section">
        <div
          class="cart-item"
          @click="$emit('click')"
        >
          <div class="cart-item-title">
            送到楼上
          </div>
          <div class="cart-item-right">
            <van-radio-group
              v-model="isUpstairs"
              @change="radioChange"
            >
              <van-radio name="0">
                否
              </van-radio>
              <van-radio name="1">
                1-3楼
              </van-radio>
              <van-radio name="2">
                4-6楼
              </van-radio>
            </van-radio-group>
          </div>
        </div>
        <CartItem
          title="餐具份数"
          :sub-head="remarkInfo.tableware || '未选择'"
          @click="showTableware=true"
        />
        <CartItem
          title="订单备注"
          :sub-head="remarkInfo.remark || '口味 偏好'"
          @click="$router.push('/remark')"
        />
      </section>

      <!-- 显示Tableware -->
      <Tableware
        :is-show="showTableware"
        @close="showTableware=false"
      />
      <!-- 优惠券列表 -->
      <van-popup
        v-model="showList"
        position="bottom"
      >
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
    <van-submit-bar
      :price="realPrice*100"
      button-text="微信支付"
      button-type="primary"
      safe-area-inset-bottom
      :loading="isPayloading"
      @submit="handlePay"
    />
  </div>
</template>

<script>
import CartItem from "../../components/Orders/CartItem";
import Tableware from "../../components/Orders/Tableware";
import { Toast } from "vant";
import { Dialog } from "vant";
export default {
  name: "Settlement",
  components: {
    CartItem,
    Tableware
  },
  data() {
    return {
      isUpstairs: "0",
      haveAddress: false,
      showTableware: false,
      addrInfo: null,
      chosenCoupon: -1,
      showList: false,
      coupons: [],
      disabledCoupons: [],
      okTime: "",
      showPicker: false,
      columns: [],
      deliveFee: 0.0,
      okFee: 0.0,
      packgeFee: 1,
      deliverTime: 30,
      isPayloading:false
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
      return (
        this.$store.getters.totalPrice -
        this.restInfo.deliveryFee +
        this.okFee +
        this.packgeFee
      );
    },
    realPrice() {
      return (
        this.$store.getters.realPrice -
        this.restInfo.deliveryFee +
        this.okFee +
        this.packgeFee
      );
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
  mounted() {
    // 监听手机物理返回键时禁止返回之前的路由
    if (window.history && window.history.pushState) {
      window.addEventListener("popstate", this.forbidBack, false);
      this.forbidBack();
    }
  },
  methods: {
    forbidBack() {
      window.history.pushState("forward", null, "#");
      window.history.forward(1);
    },
    dateFormat(fmt, date) {
      let ret;
      let opt = {
        "Y+": date.getFullYear().toString(), // 年
        "m+": (date.getMonth() + 1).toString(), // 月
        "d+": date.getDate().toString(), // 日
        "H+": date.getHours().toString(), // 时
        "M+": date.getMinutes().toString(), // 分
        "S+": date.getSeconds().toString() // 秒
        // 有其他格式化字符需求可以继续添加，必须转化成字符串
      };
      for (let k in opt) {
        ret = new RegExp("(" + k + ")").exec(fmt);
        if (ret) {
          fmt = fmt.replace(
            ret[1],
            ret[1].length == 1 ? opt[k] : opt[k].padStart(ret[1].length, "0")
          );
        }
      }
      return fmt;
    },
    radioChange() {
      switch (parseInt(this.isUpstairs)) {
        case 1:
          this.okFee = this.deliveFee + 0.5;
          break;
        case 2:
          this.okFee = this.deliveFee + 1;
          break;
        default:
          this.okFee = this.deliveFee;
          break;
      }
      if (Math.round(this.okFee) === this.okFee) {
        //是整数，折扣-0.1
        this.okFee -= 0.1;
      }
    },
    deliveryTime(time) {
      let date = new Date();
      time = parseInt(time);
      date.setMinutes(date.getMinutes() + time);
      let gethour = date.getHours();
      if (gethour < 10) {
        gethour = "0" + gethour;
      }
      let getmin = date.getMinutes();
      if (getmin < 10) {
        getmin = "0" + getmin;
      }
      return gethour + ":" + getmin;
    },
    onConfirm(okTime) {
      this.okTime = okTime;
      this.showPicker = false;
    },
    addAddress() {
      this.$router.push({
        name: "addAddress",
        params: {
          title: "添加地址提交",
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
    onBoxClick() {
      Dialog({
        message: "包装费：华广饭堂打包所需的费用"
      });
    },
    calcData() {
      //动态计算预估时间和配送费
      const roomNum = parseInt(this.restInfo.roomNum);
      let dormitory = this.addrInfo.dormitory;
      if (dormitory) {
        dormitory = dormitory.replace(/[^0-9]/gi, "");
      }
      //两个之间差值来算，把宿舍分成2个区域
      const dormNum = dormitory >= 1 && dormitory <= 6 ? 1 : 2;
      //默认起始配送费和时间为
      this.deliveFee = parseFloat(this.restInfo.deliveryFee);
      this.deliverTime = parseInt(this.restInfo.deliveryTime);
      //区域点餐
      switch (dormNum) {
        case 1: //第一个区域点餐
          switch (roomNum) {
            case 3:
              this.deliveFee += 0.5;
              this.deliverTime += 15;
              break;
            case 4:
              this.deliveFee += 1;
              this.deliverTime += 30;
              break;
            default:
              break;
          }
          break;
        case 2: //第二个区域点餐
          switch (roomNum) {
            case 1:
              this.deliveFee += 1;
              this.deliverTime += 30;
              break;
            case 2:
              this.deliveFee += 1;
              this.deliverTime += 30;
              break;
            default:
              break;
          }
          break;
        default:
          break;
      }
      //时间处理
      this.columns.push(
        "尽快送达 (" + this.deliveryTime(this.deliverTime) + "送达)"
      );
      this.okTime =
        "尽快送达 (" + this.deliveryTime(this.deliverTime) + "送达)";
      //现在距离明天24点还有多少小时
      let nowtime = new Date();
      let future = new Date();
      future.setHours(0, 0, 0, 0);
      future.setDate(future.getDate() + 1);
      let times = future.getTime() - nowtime.getTime();
      let halfhour = parseInt((times / 1000 / 60 / 60) % 24);
      for (
        let i = this.deliverTime * 2;
        i <= halfhour * 2 * 30 + 48 * 30;
        i += 30
      ) {
        if (i > halfhour * 2 * 30) {
          this.columns.push("明天 " + this.deliveryTime(i));
        } else {
          this.columns.push(this.deliveryTime(i));
        }
      }
      //食品份数
      //计算商品数量
      let foodCount = 0;
      this.orderInfo.selectFoods.forEach(element => {
        //让2份以上的商品也添加上，不然无论你点了多少分都只记录一份
        if (element.count > 1) {
          for (let i = 0; i < element.count; i++) {
            foodCount++;
          }
        } else {
          //只有一份
          foodCount++;
        }
      });
      this.packgeFee = foodCount;
      if (foodCount >= 3) {
        this.deliveFee = this.deliveFee + foodCount * 0.5;
      }
      this.okFee = this.deliveFee;
      if (Math.round(this.okFee) === this.okFee) {
        //是整数，折扣-0.1
        this.okFee -= 0.1;
      }
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
            this.calcData();
          });
        } else {
          this.calcData();
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
        if (res.data.data != 0) {
          res.data.data.forEach(element => {
            element.forEach(item => {
              item.condition = item.conditions;
            });
          });
          this.disabledCoupons = res.data.data[0];
          this.coupons = res.data.data[1];
        }
      });
    },
    handlePay() {
      if (!this.addrInfo) {
        Toast("请先选择收货地址");
        return;
      }
      //开始创建订单
      this.isPayloading = true;
      var foodArrID = []; //食物的id数组
      this.orderInfo.selectFoods.forEach(element => {
        //让2份以上的商品也添加上，不然无论你点了多少分都只记录一份
        if (element.count > 1) {
          for (let i = 0; i < element.count; i++) {
            foodArrID.push(Number(element.id));
          }
        } else {
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
      //正则取时间
      const finalTime = this.okTime
        .match(/\d+:\d+/g)
        .join("")
        .substring(0);
      let nowDate = new Date();
      if (this.okTime.search("明天") != -1) {
        //有明天
        nowDate.setDate(nowDate.getDate() + 1);
      }
      const finalFormatTime =
        this.dateFormat("YYYY-mm-dd ", nowDate) + finalTime;
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
          openID: this.userInfo.openid,
          shouldDeliveTime: finalFormatTime,
          deliveFee: this.okFee,
          upstairs: this.isUpstairs
        })
        .then(res => {
          //重要接口加错误处理！！！
          if (res.data.msg == "") {
            //微信支付
            const data = JSON.parse(res.data.data);
            // eslint-disable-next-line no-undef
            WeixinJSBridge.invoke("getBrandWCPayRequest", data, res => {
              if (res.err_msg == "get_brand_wcpay_request:ok") {
                // 使用以上方式判断前端返回,微信团队郑重提示：
                //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                Toast.success("支付成功");
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
              this.isPayloading = false;
            });
          } else {
            Dialog({
              message: "创建订单失败：" + res.data.msg
            });
          }
        });
    }
  },
  destoryed() {
    // 离开页面时销毁监听
    window.removeEventListener("popstate", this.forbidBack, false);
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
        /* display: inline-block; */
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
  }
  .cart-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4.266667vw 0;
    font-size: 1rem;
    color: #333;
    border-bottom: 1px dotted #eee;
  }
  .cart-item-title {
    font-weight: 500;
    flex: none;
  }
  .cart-item-right {
    display: flex;
    -webkit-align-items: center;
    align-items: center;
    color: #bbb;
    font-size: 0.9rem;
    max-width: 69.333333vw;
    .van-radio-group {
      display: flex;
      .van-radio {
        padding-left: 10px;
      }
    }
  }
  .checkout-section {
    margin-bottom: 2.133333vw;
    padding: 0 5.333333vw;
    background: #fff;
    box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
  }
  .delivery {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px dotted #eee;
    padding: 4.266667vw 0;
  }
  .delivery-left {
    flex: 1;
    display: flex;
    flex-direction: column;
  }
  .delivery-time {
    font-size: 1rem;
    font-weight: 500;
  }
  .delivery-right {
    text-align: right;
    display: flex;
    align-items: center;
  }
  .delivery-select {
    text-align: right;
    color: #2395ff;
  }
  .delivery-right > i {
    margin-left: 0.666667vw;
    color: #888;
    font-size: 1.2rem;
  }
}
</style>
