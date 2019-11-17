<template>
  <div class="deliver">
    <van-pull-refresh
      v-model="isLoading"
      @refresh="onRefresh"
    >
      <van-list
        v-if="!firstlogin && !nodata"
        v-model="loading"
        :finished="finished"
        finished-text="没有更多了"
        @load="onLoad"
      >
        <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
        <van-collapse
          v-for="(orderDelive,index) in orders"
          :key="index"
          v-model="activeNames"
        >
          <van-collapse-item :name="index">
            <div
              slot="title"
              class="title"
            >
              <van-tag
                v-if="orderDelive.order.status == 2 ? true : false"
                type="danger"
              >
                待取
              </van-tag>
              <van-tag
                v-if="orderDelive.order.status == 3 ? true : false"
                type="primary"
              >
                待送
              </van-tag>
              <van-tag
                v-if="orderDelive.order.status == 9 ? true : false"
                type="warning"
              >
                待支付
              </van-tag>
              <van-tag
                v-if="orderDelive.order.status == 4 ? true : false"
                type="success"
              >
                完成
              </van-tag>
              <span v-if="orderDelive.order.type==0">第{{ orderDelive.order.restNum }}饭堂</span>
              <span v-if="orderDelive.order.type==1">{{ orderDelive.order.expressAddr=='商业街京东派'?'商业街京东派':(orderDelive.order.expressAddr+'快递点') }}</span>
              <van-icon
                name="arrow"
                class="icon"
              />
              {{ orderDelive.addr.dormitory }}
              <van-tag
                v-if="orderDelive.order.isNeedFast == 1"
                round
                type="danger"
                style="margin-left:5px;text-align: center;"
              >
                +{{ orderDelive.order.fastMoney }} 元
              </van-tag>
              <div
                v-if="orderDelive.order.upstairs != 0"
                class="lou"
              >
                要上楼
              </div>
              <div class="end">
                <van-icon
                  name="clock-o"
                  class="icon"
                />
                {{ orderDelive.order.shouldDeliveTime }}送达
              </div>
            </div>
            <div class="info">
              <div class="user">
                <div class="usernameAndPhone">
                  <div class="username">
                    <van-icon
                      name="manager"
                      class="icon"
                    />
                    <div class="name">
                      {{ orderDelive.addr.name }}{{ orderDelive.addr.gender==1?'先生':'小姐' }}
                      <p>
                        <a :href="'tel:'+orderDelive.addr.phone">({{ orderDelive.addr.phone }})</a>
                      </p>
                    </div>
                  </div>
                  <!--                   <div class="userphone">
                    <van-icon
                      name="phone"
                      class="icon"
                    />
                    <div class="phoneno">
                      <a :href="'tel:'+orderDelive.addr.phone">{{ orderDelive.addr.phone }}</a>
                    </div>
                  </div>-->
                </div>
                <div
                  class="notes"
                  @click="remarkClick(orderDelive.order.remark)"
                >
                  <van-icon
                    name="comment"
                    class="icon icon-right"
                  />
                  <div class="note">
                    {{ handleRemarkData(orderDelive.order.remark) }}
                  </div>
                </div>
              </div>
              <div class="restAndAddr">
                <div
                  v-if="orderDelive.order.type==0"
                  class="rest"
                >
                  <van-icon
                    name="shop"
                    class="icon"
                  />
                  <div
                    class="res"
                  >
                    {{ orderDelive.order.restNum }}饭{{ orderDelive.order.restName }} {{ orderDelive.order.location }}
                  </div>
                </div>
                <div
                  v-if="orderDelive.order.type==1"
                  class="rest"
                >
                  <van-icon
                    name="send-gift"
                    class="icon"
                  />
                  <div class="res">
                    {{ orderDelive.order.expressAddr }}快递点
                  </div>
                </div>
                <div class="address">
                  <van-icon
                    name="location"
                    class="icon icon-right"
                  />
                  <div
                    class="addr"
                  >
                    {{ orderDelive.addr.dormitory }} {{ orderDelive.addr.roomNum }}{{ orderDelive.order.upstairs>0?'要上楼':'' }}
                  </div>
                </div>
              </div>
            </div>
            <van-divider />
            <div
              v-if="orderDelive.order.type==0"
              class="foods"
            >
              <ul>
                <li
                  v-for="(food,indexFood) in orderDelive.order.foodsArr"
                  :key="indexFood"
                >
                  <div class="food">
                    <div class="name">
                      {{ indexFood+1 }}.{{ food.name }}
                    </div>
                    <div class="price">
                      ¥{{ parseFloat(food.price).toFixed(2) }}
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <van-divider v-show="orderDelive.order.type==0" />
            <div class="bottom">
              <div class="texts">
                <div
                  v-if="orderDelive.order.type==0"
                  class="totalMoney"
                >
                  <div class="totalTitle">
                    商品总价
                  </div>
                  <div
                    class="totalPrice"
                  >
                    ¥{{ parseFloat(orderDelive.order.totalPrice).toFixed(2) - parseFloat(orderDelive.order.deliveFee).toFixed(2) }}
                  </div>
                </div>
                <div
                  v-if="orderDelive.order.type==1"
                  class="totalMoney"
                >
                  <div class="totalTitle">
                    重量
                  </div>
                  <div
                    class="totalPrice"
                    style="color:#5a5a5a"
                  >
                    {{ handleWeightData(orderDelive.order.weight) }}
                  </div>
                </div>
                <div class="incomeMoney">
                  <div class="totalTitle">
                    配送费
                  </div>
                  <div class="totalPrice">
                    ¥{{ parseFloat(orderDelive.order.deliveFee).toFixed(2) }}
                  </div>
                </div>
              </div>
              <div class="buttons">
                <div
                  v-if="orderDelive.order.status == 2 ? true : false"
                  class="orderThisButton"
                >
                  <van-button
                    type="info"
                    style="width: 100%"
                    round
                    @click="onGetGoodsButtonClick(orderDelive.order.id,orderDelive.id,index)"
                  >
                    已取到商品
                  </van-button>
                </div>
                <div
                  v-if="orderDelive.order.status == 9 ? true : false"
                  class="compensateText"
                >
                  客服已介入，请等待用户支付尾款后再继续派送！
                </div>
                <div
                  v-if="orderDelive.order.status == 3 ? true : false"
                  class="deliveButton"
                >
                  <div
                    v-if="orderDelive.order.type == 1"
                    style="width:100%;display:flex;justify-content: space-around;"
                    class="onExpressButtons"
                  >
                    <van-button
                      type="warning"
                      style="width: 40%;"
                      round
                      @click="onNotWeightButtonClick(orderDelive.order.id,orderDelive.id,index)"
                    >
                      重量不符
                    </van-button>
                    <van-button
                      type="primary"
                      style="width: 40%;margin-left:5%;"
                      round
                      @click="onExpressDeliveButtonClick(orderDelive.order.id,orderDelive.id,index,userInfo.id)"
                    >
                      我已送达
                    </van-button>
                  </div>
                  <div
                    v-if="orderDelive.order.type == 0"
                    class="onDeliveButtons"
                  >
                    <van-button
                      type="primary"
                      style="width: 100%;"
                      round
                      @click="onDeliveButtonClick(orderDelive.order.id,orderDelive.id,index,userInfo.id)"
                    >
                      我已送达
                    </van-button>
                  </div>
                </div>
                <div
                  v-if="orderDelive.order.status == 4 ? true : false"
                  class="finishButton"
                >
                  <van-button
                    type="success"
                    style="width: 100%"
                    round
                  >
                    已完成
                  </van-button>
                </div>
              </div>
            </div>
          </van-collapse-item>
        </van-collapse>
      </van-list>
    </van-pull-refresh>
    <div
      v-if="firstlogin"
      class="nologin"
    >
      <NoLoginInfo />
    </div>
    <div
      v-if="nodata"
      class="nologin"
    >
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
  name: "Deliver",
  components: {
    NoLoginInfo,
    NoDeliveOrderInfo
  },
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
      activeNames: [],
      isLoading: false,
    };
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  created() {
    this.getData();
  },
  methods: {
    onExpressDeliveButtonClick(orderID, iD, indexx, deID) {
      Dialog.confirm({
        title: "确定已送达到客户手上了吗？"
      })
        .then(() => {
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.changToFinishExpressDelive",
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
    },
    onNotWeightButtonClick(orderID, iD, indexx){
      Dialog.confirm({
        title: "注意",
        message: "确定实际拿到的快递与用户下单的重量不相符吗？确定后请联系客服，将提醒用户支付 3元 配送费尾款给您后再继续派送"
      })
        .then(() => {
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.changToCompensate",
            {
              params: {
                orderID: orderID
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              this.$set(this.orders[indexx].order, "status", 9); //让开头的图标变化
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
    remarkClick(remark) {
      Dialog({ title: "订单备注", message: remark });
    },
    handleRemarkData(remark) {
      if (remark != "无") {
        if (remark.length > 8) {
          return remark.substring(0, 8) + "...";
        }
      }
      return remark;
    },
    handleWeightData(weight) {
      weight = parseInt(weight);
      switch (weight) {
        case 0:
          return "小(约0～3瓶中型怡宝)";
        case 1:
          return "中(约1瓶大型怡宝)";
        case 2:
          return "大(约1箱牛奶)";
        case 3:
          return "特大(约2箱牛奶)";
        case 4:
          return ">2箱牛奶重或者体积大";
        default:
          break;
      }
    },
    getData() {
      this.firstlogin = localStorage.firstlogin == 0 ? false : true;
      this.firstLoadData();//可以参考用户端order.vue后期去掉
    },
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios("https://takeawayapi.pykky.com/?s=Deliverorders.GetAllOrder", {
        params: {
          deliverID: this.userInfo.id,
          page: this.offset
        }
      }).then(res => {
        if (JSON.stringify(res.data.data) == "{}") {
          this.finished = true;
          this.loading = false;
          this.nodata = true;
          return;
        }
        this.orderlist = res.data.data;
        this.handleData();
      });
    },
    handleData() {
      //对商品数据处理
      this.orderlist.forEach(orders => {
        if (orders.order.type == 0) {
          var OrderFoods = JSON.parse(orders.order.foods);
          var foodsArr = new Array();
          OrderFoods.forEach(id => {
            orders.food.forEach(food => {
              if (food.id == id) {
                foodsArr.push(food); //把每行food的全部数据对象都放入数组
              }
            });
          });
          orders.order.foodsArr = foodsArr;
          orders.order.foodsCount = foodsArr.length; //食物数量
        }
        this.orders.push(orders);
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          //this.offset += parseInt(this.orders[this.orders.length - 1].id);
          this.offset++;
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.GetAllOrder",
            {
              params: {
                deliverID: this.userInfo.id,
                page: this.offset
              }
            }
          ).then(res => {
            if (JSON.stringify(res.data.data) != "{}") {
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
    onRefresh() {
      this.orders = [];
      this.orderlist = [];
      this.size = 5;
      this.finished = false;
      this.loading = true;
      this.firstLoadData();
      this.onLoad();
      this.isLoading = false;
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
    onDeliveButtonClick(orderID, iD, indexx, deID) {
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
  }
};
</script>

<style lang="scss" scoped>
.deliver {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .van-list {
    height: calc(100vh - 50px);
  }
  .title {
    display: flex;
    align-items: center;
    .van-tag {
      margin-right: 5px;
    }
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
    .van-icon {
      display: flex;
      padding-bottom: 5px;
    }
    .icon-right {
      justify-content: flex-end;
    }
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
    flex-wrap: wrap;
    .deliveButton{
      width: 100%;
    }
    .texts {
      width: 100%;
      display: flex;
      justify-content: space-between;
      padding: 0 20px;
      .totalTitle {
        color: #323233;
      }
      .totalPrice {
        color: red;
        font-size: 15px;
        display: flex;
        justify-content: flex-end;
      }
    }
    .buttons {
      width: 100%;
      margin-top: 16px;
      .compensateText{
        margin-top: 16px;
        text-align: center;
      }
    }
  }
  .nologin {
    height: 100%;
    display: flex;
    align-items: center;
  }
}
</style>