<template>
  <div class="deliver">
    <van-list
      v-if="!firstlogin && !nodata"
      v-model="loading"
      :finished="finished"
      finished-text="没有更多了"
      @load="onLoad"
    >
      <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
      <van-collapse v-model="activeNames" v-for="(orderDelive,index) in orders" :key="index">
        <van-collapse-item :name="index">
          <div slot="title" class="title">
            <van-tag v-if="orderDelive.order.status == 2 ? true : false" type="danger">待取</van-tag>
            <van-tag v-if="orderDelive.order.status == 3 ? true : false" type="primary">待送达</van-tag>
            <van-tag v-if="orderDelive.order.status == 4 ? true : false" type="success">已完成</van-tag>
            &nbsp&nbsp第{{orderDelive.order.restNum}}饭堂
            <van-icon name="arrow" class="icon" />
            {{orderDelive.addr.dormitory}}
            <div class="end">
              <van-icon name="clock-o" class="icon" />
              {{orderDelive.order.shouldDeliveTime}}
            </div>
          </div>
          <div class="info">
            <div class="user">
              <div class="usernameAndPhone">
                <div class="username">
                  <van-icon name="manager" class="icon" />
                  <div
                    class="name"
                  >{{orderDelive.addr.name}}{{orderDelive.addr.gender==1?'先生':'小姐'}}</div>
                </div>
                <div class="userphone">
                  <van-icon name="phone" class="icon" />
                  <div class="phoneno">{{orderDelive.addr.phone}}</div>
                </div>
              </div>
              <div class="notes">
                <van-icon name="comment" class="icon" />
                <div class="note">{{orderDelive.order.remark}}</div>
              </div>
            </div>
            <div class="restAndAddr">
              <div class="rest">
                <van-icon name="shop" class="icon" />
                <div
                  class="res"
                >{{orderDelive.order.restNum}}饭{{orderDelive.order.restName}} {{orderDelive.order.location}}</div>
              </div>
              <div class="address">
                <van-icon name="location" class="icon" />
                <div class="addr">{{orderDelive.addr.dormitory}} {{orderDelive.addr.roomNum}}</div>
              </div>
            </div>
          </div>
          <van-divider />
          <div class="foods">
            <ul>
              <li v-for="(food,indexFood) in orderDelive.order.foodsArr" :key="indexFood">
                <div class="food">
                  <div class="name">{{indexFood+1}}.{{food.name}}</div>
                  <div class="price">¥{{parseFloat(food.price).toFixed(2)}}</div>
                </div>
              </li>
            </ul>
          </div>
          <van-divider />
          <div class="bottom">
            <div class="totalMoney">
              <div class="totalTitle">商品总价：</div>
              <div
                class="totalPrice"
              >¥{{parseFloat(orderDelive.order.totalPrice).toFixed(2) - parseFloat(orderDelive.order.deliveFee).toFixed(2)}}</div>
            </div>
            <div class="incomeMoney">
              <div class="totalTitle">可得配送费：</div>
              <div class="totalPrice">¥{{parseFloat(orderDelive.order.deliveFee).toFixed(2)}}</div>
            </div>
            <div class="orderThisButton" v-if="orderDelive.order.status == 2 ? true : false">
              <van-button
                type="info"
                @click="onGetGoodsButtonClick(orderDelive.order.id,orderDelive.id,index)"
              >已取到商品</van-button>
            </div>
            <div class="deliveButton" v-if="orderDelive.order.status == 3 ? true : false">
              <van-button
                type="primary"
                @click="onDeliveButtonClick(orderDelive.order.id,orderDelive.id,index,userInfo.id)"
              >我已送达</van-button>
            </div>
            <div class="finishButton" v-if="orderDelive.order.status == 4 ? true : false">
              <van-button type="success">已完成</van-button>
            </div>
          </div>
        </van-collapse-item>
      </van-collapse>
    </van-list>
    <div class="nologin" v-if="firstlogin">
      <NoLoginInfo />
    </div>
    <div class="nologin" v-if="nodata">
      <NoDeliveOrderInfo />
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
import { Toast } from "vant";
import NoLoginInfo from "../components/NoLoginInfo";
import NoDeliveOrderInfo from "../components/NoDeliveOrderInfo";

export default {
  name: "deliver",
  data() {
    return {
      orderlist: [], //存放当前订单容器
      orders: [], //存放所有订单容器
      offset: 1,
      size: 5,
      firstlogin: false,
      nodata: false,
      loading: false,
      finished: false,
      activeNames: []
    };
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.firstlogin = localStorage.firstlogin == 0 ? false : true;
      this.firstLoadData();
    },
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Deliverorders.GetAllOrder",
        {
          params: {
            deliverID: this.userInfo.id,
            offset: this.offset,
            limit: this.size
          }
        }
      ).then(res => {
        if (JSON.stringify(res.data.data) == "{}") {
          this.finished = true;
          this.loading = false;
          this.nodata = true;
          return;
        }
        this.orderlist = res.data.data;
        this.orders = res.data.data;
        this.handleData();
      });
    },
    handleData() {
      //对商品数据处理
      var i = 0;
      this.orderlist.forEach(orders => {
        var showfood = "";
        var OrderFoods = JSON.parse(orders.order.foods);
        var foodsArr = new Array();
        OrderFoods.forEach(id => {
          orders.food.forEach(food => {
            if (food.id == id) {
              foodsArr.push(food); //把每行food的全部数据对象都放入数组
            }
          });
        });
        this.orders[i].order.foodsArr = foodsArr;
        this.orders[i].order.foodsCount = foodsArr.length; //食物数量
        i++;
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          this.offset += 5;
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.GetAllOrder",
            {
              params: {
                deliverID: this.userInfo.id,
                offset: this.offset,
                limit: this.size
              }
            }
          ).then(res => {
            if (res.data.length > 0) {
              this.orderlist = res.data.data;
              this.handleData();
              this.loading = false;
              if (res.data < this.size) {
                this.finished = true;
                this.loading = false;
              }
            } else {
              // 数据为空
              this.finished = true;
              this.loading = false;
            }
          });
        }
      }, 500);
    },
    onGetGoodsButtonClick(orderID, iD, indexx) {
      Dialog.confirm({
        title: "确定取到商品了吗？"
      })
        .then(() => {
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.ChangToGetFood",
            {
              params: {
                orderID: orderID,
                ID: iD
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              this.$set(this.orders[indexx].order, "status", 3); //让开头的图标变化
              Toast.success("成功");
            } else {
              Toast.fail("失败！" + res.data.msg);
            }
          });
        })
        .catch(() => {
          // on cancel
        });
    },
    onDeliveButtonClick(orderID, iD, indexx,deID) {
      Dialog.confirm({
        title: "确定已送达到客户手上了吗？"
      })
        .then(() => {
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.ChangToFinishDelive",
            {
              params: {
                orderID: orderID,
                ID: iD,
                deliverID: deID
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              this.$set(this.orders[indexx].order, "status", 4); //让开头的图标变化
              this.activeNames.forEach((item, index) => {
                if (item == indexx) {
                  this.activeNames.splice(index, 1);
                }
              });
              Toast.success("成功");
            } else {
              Toast.fail("失败！" + res.data.msg);
            }
          });
        })
        .catch(() => {
          // on cancel
        });
    }
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  components: {
    NoLoginInfo,
    NoDeliveOrderInfo
  }
};
</script>

<style lang="scss" scoped>
.deliver {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .title {
    display: flex;
    align-items: center;
    .icon {
      padding: 0 5px;
    }
    .end {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      flex-grow: 1;
    }
  }
  .info {
    color: rgb(90, 90, 90);
    padding: 0 20px;
    .user {
      display: flex;
      justify-content: space-between;
    }
    .restAndAddr {
      display: flex;
      justify-content: space-between;
      padding-top: 15px;
    }
    .username {
      margin-bottom: 5px;
    }
    .rest {
      margin-bottom: 5px;
    }
  }
  .foods {
    padding: 0 10px;
    .food {
      display: flex;
      justify-content: space-between;
    }
  }
  .bottom {
    display: flex;
    justify-content: space-around;
    .totalTitle {
      color: black;
    }
    .totalPrice {
      color: red;
      font-size: 15px;
    }
  }
  .nologin {
    height: 100%;
    display: flex;
    align-items: center;
  }
}
</style>