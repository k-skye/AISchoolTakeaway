<template>
  <div class="orderInfo">
    <van-popup
      v-model="show"
      closeable
      :style="{ height: '30%',width: '80%' }"
    >
      <div
        v-if="orderDetail.deliverName"
        class="head"
      >
        投诉伙伴 {{ orderDetail.deliverName }}
      </div>
      <van-cell-group title=" ">
        <van-field
          v-model="message"
          type="textarea"
          placeholder="请输入投诉"
          rows="1"
          autosize
          clearable
        >
          <van-button
            slot="button"
            size="small"
            type="primary"
            @click="onSendButtonClick"
          >
            投诉
          </van-button>
        </van-field>
      </van-cell-group>
    </van-popup>
    <div class="header">
      <van-nav-bar
        title="订单详情"
        left-arrow
        @click-left="$router.go(-1)"
      />
    </div>
    <div
      v-if="orderDetail"
      class="view-body"
    >
      <div class="status-head">
        <div class="status-text">
          {{ statusText }}
        </div>
        <div class="status-title">
          {{ statusContent }}
        </div>
        <div class="buttons">
          <!-- 付款后 -->
          <van-button
            v-if="orderDetail.status==1"
            type="info"
            @click="clickCancelOrder"
          >
            取消订单
          </van-button>
          <!--            待取餐时 
          <van-button type="info" v-if="orderDetail.status==2">申请退款</van-button>-->
          <!-- 已送达时 -->
          <van-button
            v-if="orderDetail.status==4 && orderDetail.type==0"
            type="primary"
            @click="$router.push({name: 'comment',params: {restID: orderDetail.restID, userID: userInfo.id, orderID: orderDetail.id}})"
          >
            评价
          </van-button>
          <van-button
            v-if="orderDetail.status>=4 && orderDetail.status<=6 && orderDetail.hasComplaint==0"
            class="Complaint"
            type="info"
            @click="showPopup"
          >
            投诉伙伴
          </van-button>
          <!-- 需要付尾款时 -->
          <van-button
            v-if="orderDetail.status==9"
            type="primary"
            size="large"
            @click="handlePay(orderDetail.id)"
          >
            <p style="display:flex;align-items: center;justify-content: center;">
              <van-icon
                name="wechat"
                size="1.5rem"
              />
              支付尾款
            </p>
          </van-button>
        </div>
      </div>
      <!-- 伙伴信息 -->
      <div
        v-if="orderDetail.deliverName && orderDetail.status<4"
        class="detail-card"
      >
        <div class="title">
          伙伴
        </div>
        <ul class="card-list">
          <li class="list-item">
            <span>姓名：{{ orderDetail.deliverName }}</span>
          </li>
          <li class="list-item">
            <span>
              手机：
              <a :href="'tel:'+orderDetail.deliverPhone">{{ orderDetail.deliverPhone }}</a>
            </span>
          </li>
        </ul>
      </div>
      <div
        v-if="orderDetail.restName"
        class="restaurant-card"
      >
        <!-- 点餐内容 -->
        <section class="checkout-section cart-group">
          <h3>{{ orderDetail.restName }}</h3>
          <ul v-if="orderDetail.selectFoods">
            <li
              v-for="(food,index) in orderDetail.selectFoods"
              :key="index"
            >
              <img
                :src="'https://takeawayschool.oss-cn-shenzhen.aliyuncs.com/goodImgs/'+food.logo"
                alt
              >
              <div class="cart-group-info">
                <span>{{ food.name }}</span>
                <span>¥{{ food.price }}</span>
              </div>
            </li>
            <li class="cart-group-deliveryFee">
              <div class="deliverTitle">
                伙伴费
              </div>
              <div>¥{{ orderDetail.deliveryFee }}</div>
            </li>
            <li class="cart-group-discount">
              <div class="discountTitle">
                红包
              </div>
              <div>- ¥{{ (orderDetail.value/100) }}</div>
            </li>
            <li class="cart-group-total">
              <div class="discounts" />
              <div class="subtotal">
                <span class="discot">
                  已优惠
                  <span class="red">¥{{ (orderDetail.value/100) }}</span>
                </span>
                <span>小计 ¥</span>
                <span class="price">{{ (parseFloat(orderDetail.payPrice)/100).toFixed(2) }}</span>
              </div>
            </li>
          </ul>
        </section>
      </div>

      <!-- 快递信息 -->
      <div
        v-if="orderDetail.remark"
        class="detail-card"
      >
        <div class="title">
          快递
        </div>
        <ul class="card-list">
          <li class="list-item">
            <span>取件地: {{ orderDetail.expressAddr }}</span>
          </li>
          <li class="list-item">
            <span>取件码: {{ orderDetail.expressCode }}</span>
          </li>
          <li class="list-item">
            <span>重量: {{ orderDetail.weight }}</span>
          </li>
          <li class="list-item">
            <span>类型: {{ orderDetail.goodType }}</span>
          </li>
          <div
            v-if="orderDetail.isNeedFast > 1"
            class="needFast"
          >
            <li class="list-item">
              <span>需要加急: 是</span>
            </li>
            <li class="list-item">
              <span>加急红包金额: {{ (parseFloat(orderDetail.fastMoney)).toFixed(2) }}</span>
            </li>
          </div>
        </ul>
      </div>

      <!-- 配送信息 -->
      <div class="detail-card">
        <div class="title">
          配送
        </div>
        <ul class="card-list">
          <li class="list-item">
            <span>送达时间：</span>
            <div v-if="orderDetail.delivedTime">
              {{ orderDetail.delivedTime }}
            </div>
            <div v-else>
              {{ orderDetail.shouldDeliveTime }}
            </div>
          </li>
          <li
            v-if="orderDetail.addrInfo"
            class="list-item"
          >
            <!-- 因为请求是异步获取数据，所以最先开始addrInfo是一个空对象 -->
            <span>送货地址：</span>
            <div class="content">
              <span>{{ orderDetail.addrInfo.name }} {{ orderDetail.addrInfo.gender==1?'先生':'小姐' }}</span>
              <span>{{ orderDetail.addrInfo.phone }}</span>
              <span>{{ orderDetail.addrInfo.dormitory }} {{ orderDetail.addrInfo.roomNum }}</span>
            </div>
          </li>
        </ul>
      </div>

      <!-- 订单信息 -->
      <div
        v-if="orderDetail.remark"
        class="detail-card"
      >
        <div class="title">
          订单
        </div>
        <ul class="card-list">
          <li class="list-item">
            <span>下单时间: {{ orderDetail.createTime }}</span>
          </li>
          <li class="list-item">
            <span>订单备注: {{ orderDetail.remark }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
import { Toast } from 'vant';
export default {
  name: "OrderInfo",
  data() {
    return {
      message: "",
      show: false,
      orderDetail: {},
      statusText: "订单已送达",
      statusContent: "感谢您对我们外卖服务的信任, 期待再次光临"
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.orderDetail = to.params;
      vm.getData();
    });
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  methods: {
    handlePay(orderid){
      this.$axios
        .post("http://tatestapi.pykky.com/?s=Tradinglog.CreateOneCompensate", {
          orderID: orderid,
          openID: this.userInfo.openid,
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
            });
          } else {
            Dialog({
              message: "创建订单失败：" + res.data.msg
            });
          }
        });
    },
    onSendButtonClick(){
      this.$axios.post(
        "http://tatestapi.pykky.com/?s=Feedback.addOneComplaintByUser",
        {
            userID: this.userInfo.id,
            content: this.message,
            deliverID: this.orderDetail.deliverID,
            orderID: this.orderDetail.id
        }
      ).then(res => {
        if (res.data.data == 'ok') {
          Toast.success("投诉成功");
          this.$router.push('order');
        }else{
          Toast.fail("投诉失败！"+res.data.msg);
        }
      });
    },
    showPopup() {
      this.show = true;
    },
    clickCancelOrder() {
      Dialog.confirm({
        title: "取消订单",
        message: "确定要取消吗？钱将退回您的微信零钱"
      })
        .then(() => {
          this.$axios
            .post("http://tatestapi.pykky.com/?s=Orders.CancelOrder", {
              id: this.orderDetail.id
            })
            .then(res => {
              if (res.data.data == "ok") {
                Toast.success("取消成功,已退回微信零钱");
                this.$router.push("order");
              } else {
                Toast.fail("取消失败！" + res.data.msg);
              }
            });
        })
        .catch(() => {
          // on cancel
        });
    },
    getData() {
      const status = parseInt(this.orderDetail.status);
      switch (status) {
        case 0:
          this.statusText = "未支付";
          this.statusContent = "此订单稍后将自动关闭，您可去重新下单。";
          break;
        case 1:
          this.statusText = "待接单";
          this.statusContent = "如在30分钟后仍未被伙伴接单，将自动取消";
          break;
        case 2:
          this.statusText = "待取货";
          this.statusContent = "伙伴正在前往取商品的路上";
          break;
        case 3:
          this.statusText = "配送中";
          this.statusContent = "伙伴已取到商品，正在飞奔到您身边";
          break;
        case 4:
          this.statusText = "已送达";
          this.statusContent = "伙伴已确认送达，如您未收到可联系客服";
          break;
        case 5:
          this.statusText = "已评价";
          this.statusContent = "已收到您的评价，待伙伴回复你噢～";
          break;
        case 6:
          this.statusText = "已回复评价";
          this.statusContent = "伙伴已回复你的评论啦！";
          break;
        case 7:
          this.statusText = "退款中";
          this.statusContent = "正在等待客服确认";
          break;
        case 8:
          this.statusText = "已关闭";
          this.statusContent = "此订单已关闭，您可去重新下单。";
          break;
        case 9:
          this.statusText = "待支付尾款";
          this.statusContent = "快递实际重量与您下单填写的不符，需要支付 3元 尾款才能继续配送。";
          break;
        default:
          this.statusText = "订单异常";
          this.statusContent = "如有疑问请联系客服。";
          break;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
.orderInfo {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  overflow: auto;
  .head{
    text-align: center;
    padding-top: 15px;
  }
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
      .buttons {
        padding: 10px 5px 0;
        .Complaint {
          margin-left: 5px;
        }
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
      padding: 0 3.2vw 2.333333vw;
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

