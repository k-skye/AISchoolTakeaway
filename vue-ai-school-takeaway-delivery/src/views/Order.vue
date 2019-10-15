<template>
  <div class="order">
    <div v-if="!firstlogin" class="header">
      <div class="headerContain">
        <!-- <div class="orderButton">暂时不用，未来可能用到
          <van-switch :value="orderChecked" @input="onOrderInput" />
          <div class="headerTitle">{{orderChecked ? '接单提醒:开' : '接单提醒:关'}}</div>
        </div>-->
        <div class="headerEndButton">
          <van-icon :size="28" name="setting-o" @click="onSetting" />
        </div>
      </div>
    </div>
    <van-popup
      v-if="!firstlogin"
      v-model="showRule"
      closeable
      position="bottom"
      :style="{ height: '38%' }"
    >
      <div class="settings">
        <div class="head">筛选</div>
        <div class="chooses">
          <Choose
            label="饭堂"
            :value="chooseRestValue"
            @getValue="getRestValue"
            placeholder="选择饭堂"
            :data="rests"
          />
          <Choose
            label="收货地址"
            :value="chooseAddrValue"
            @getValue="getAddrValue"
            placeholder="选择收货地址"
            :data="addrs"
          />
        </div>
        <div class="isNear">
          <div class="title">包括附近的宿舍</div>
          <div class="nearSwitch">
            <van-switch :value="nearChecked" @input="onNearInput" />
          </div>
        </div>
      </div>
    </van-popup>
    <div v-if="!firstlogin" class="content">
      <van-pull-refresh v-model="isLoading" @refresh="onRefresh">
        <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
          <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
          <van-collapse v-model="activeNames" v-for="(order,index) in orders" :key="index">
            <van-collapse-item :name="index" :icon="order.haveSumit == 1 ? 'checked' : 'shop-o'">
              <div slot="title" class="title">
                第{{order.restNum}}饭堂
                <van-icon name="arrow" class="icon" />
                {{order.dormitory}}
                <div class="end">
                  <van-icon name="clock-o" class="icon" />
                  {{order.shouldDeliveTime}}
                  <van-icon name="bag-o" class="icon" />
                  x{{order.foodsCount}}
                  <!-- <van-icon name="contact" class="icon" />男 -->
                </div>
              </div>
              <div class="foods">
                <ul>
                  <li v-for="(food,indexFood) in order.foodsArr" :key="indexFood">
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
                  >¥{{parseFloat(order.totalPrice).toFixed(2) - parseFloat(order.deliveFee).toFixed(2)}}</div>
                </div>
                <div class="incomeMoney">
                  <div class="totalTitle">可得配送费：</div>
                  <div class="totalPrice">¥{{parseFloat(order.deliveFee).toFixed(2)}}</div>
                </div>
                <div class="orderThisButton">
                  <van-button v-if="order.status == 2 ? true : false" type="info">已接单</van-button>
                  <van-button
                    v-else
                    type="primary"
                    @click="onOrderButtonClick(order.id,index)"
                  >&nbsp接单&nbsp</van-button>
                </div>
              </div>
            </van-collapse-item>
          </van-collapse>
        </van-list>
      </van-pull-refresh>
    </div>
    <div class="nologin" v-else>
      <NoLoginInfo />
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
import Choose from "../components/Choose";
import { Toast } from "vant";
import NoLoginInfo from "../components/NoLoginInfo";

export default {
  name: "deliver",
  data() {
    return {
      orderlist: [], //存放当前订单容器
      orders: [], //存放所有订单容器
      offset: 1,
      size: 5,
      orderChecked: false,
      loading: false,
      finished: false,
      showRule: false,
      chooseRestValue: "",
      rests: [
        "全部",
        "第一饭堂+第二饭堂",
        "第三饭堂",
        "第四饭堂",
        "校门口+其他"
      ],
      addrs: [
        "全部",
        "C1",
        "C2",
        "C3",
        "C4",
        "C5",
        "C6",
        "C7",
        "C8",
        "C9",
        "C10",
        "C11",
        "C12",
        "C13",
        "C14",
        "C15",
        "C16",
        "C17",
        "C18",
        "C19",
        "C20",
        "C21",
        "C22",
        "C23"
      ],
      chooseAddrValue: "",
      nearChecked: true,
      activeNames: [],
      firstlogin: false,
      isLoading: false,
      chooseRestNum: 0,
      chooseAddrNum: 0,
      chooseNearNum: 1
    };
  },
  created() {
    this.getData();
  },
  methods: {
    getData() {
      this.firstlogin = localStorage.firstlogin == 0 ? false : true;
      this.chooseRestNum = this.userInfo.chooseRest;
      this.chooseAddrNum = this.userInfo.chooseAddr;
      this.chooseNearNum = this.userInfo.chooseNear;
      //筛选
      this.nearChecked = this.chooseNearNum == "1" ? true : false;
      switch (this.chooseRestNum) {
        case "0":
          this.chooseRestValue = "全部";
          break;
        case "1":
          this.chooseRestValue = "第一饭堂+第二饭堂";
          break;
        case "2":
          this.chooseRestValue = "第三饭堂";
          break;
        case "3":
          this.chooseRestValue = "第四饭堂";
          break;
        case "4":
          this.chooseRestValue = "校门口+其他";
          break;
        default:
          break;
      }
      switch (this.chooseAddrNum) {
        case "0":
          this.chooseAddrValue = "全部";
          break;
        default:
          //其他数字-正则匹配
          const str = "C" + this.chooseAddrNum;
          this.chooseAddrValue = str;
          break;
      }
      this.firstLoadData();
    },
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Orders.GetAllNeedDeliveOrders",
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
      this.orderlist.forEach(order => {
        var showfood = "";
        var OrderFoods = JSON.parse(order.foods);
        var foodsArr = new Array();
        OrderFoods.forEach(id => {
          order.food.forEach(food => {
            if (food.id == id) {
              foodsArr.push(food); //把每行food的全部数据对象都放入数组
            }
          });
        });
        this.orders[i].foodsArr = foodsArr;
        this.orders[i].foodsCount = foodsArr.length; //食物数量
        i++;
      });
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
    onOrderInput(orderChecked) {
      Dialog.confirm({
        title: "是否开/关？",
        message:
          "注：接单开启后就算您关闭本页面也会在微信继续收到筛选后的接单提醒噢～"
      }).then(() => {
        this.orderChecked = orderChecked;
      });
    },
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        if (!this.finished) {
          this.offset += 5;
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Orders.GetAllNeedDeliveOrders",
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
    onSetting() {
      this.showRule = true;
    },
    getRestValue(data) {
      this.chooseRestValue = data;
      var needNum = 0;
      switch (data) {
        case "全部":
          needNum = 0;
          break;
        case "第一饭堂+第二饭堂":
          needNum = 1;
          break;
        case "第三饭堂":
          needNum = 2;
          break;
        case "第四饭堂":
          needNum = 3;
          break;
        case "校门口+其他":
          needNum = 4;
          break;
        default:
          needNum = 0;
          break;
      }
      this.chooseRestNum = needNum;
      this.valueChange();
    },
    getAddrValue(data) {
      this.chooseAddrValue = data;
      var needNum = 0;
      switch (data) {
        case "全部":
          needNum = 0;
          break;
        default:
          //其他数字-正则匹配
          const num = data.replace(/[^0-9]/gi, "");
          needNum = num;
          break;
      }
      this.chooseAddrNum = needNum;
      this.valueChange();
    },
    onNearInput() {
      this.nearChecked = !this.nearChecked;
      if (this.nearChecked) {
        this.chooseNearNum = 1;
      } else {
        this.chooseNearNum = 0;
      }
      this.valueChange();
    },
    valueChange() {
      this.$axios(
        "https://takeawayapi.pykky.com/?s=DeliverUsers.ChangeUserInfoOnChooseByUserId",
        {
          params: {
            userID: this.userInfo.id,
            chooseAddr: this.chooseAddrNum,
            chooseRest: this.chooseRestNum,
            chooseNear: this.chooseNearNum
          }
        }
      ).then(res => {
        if (res.data.data == "ok") {
          Toast.success("修改成功");
        } else {
          Toast.fail("失败！" + res.data.msg);
        }
      });
      const openid = localStorage.openid;
      //用openid去get全部用户信息回来
      this.$axios("https://takeawayapi.pykky.com/?s=DeliverUsers.GetUserInfo", {
        params: {
          openid: openid
        }
      }).then(res => {
        this.$store.dispatch("setUserInfo", res.data.data);
      });
    },
    onOrderButtonClick(orderID, indexx) {
      Dialog.confirm({
        title: "确定接单吗？",
        message: "注：如果接单后不送将受到相应处罚甚至封号！"
      })
        .then(() => {
          //开始创建订单
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Deliverorders.CreateOneOrder",
            {
              params: {
                orderID: orderID,
                deliverID: this.userInfo.id
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              this.$set(this.orders[indexx], "status", 2); //让开头的图标变成打勾
              //this.orders[indexx].haveSumit == 1; 用上面来替代掉这句，才能让vue刷新视图里的数据
              this.activeNames.forEach((item, index) => {
                if (item == indexx) {
                  this.activeNames.splice(index, 1);
                }
              });
              Toast.success("接单成功");
            } else {
              Toast.fail("接单失败！" + res.data.msg);
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
    Choose,
    NoLoginInfo
  }
};
</script>

<style lang="scss" scoped>
.order {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .header {
    width: 100%;
    height: 40px;
    background-color: white;
    border-bottom: 1px rgb(218, 218, 218) solid;
    position: fixed;
    z-index: 999;
    display: flex;
    justify-content: center;
    flex-direction: column;
    .headerContain {
      margin-left: 8px;
      display: flex;
      align-items: center;
      justify-content: flex-end; //只有一个元素的时候用，让设置按钮居右显示
      //justify-content: space-between;
      .orderButton {
        display: flex;
        align-items: center;
        .headerTitle {
          margin-left: 8px;
        }
      }
      .headerEndButton {
        display: flex;
        margin-right: 10px;
      }
    }
  }
  .settings {
    .head {
      font-size: 35px;
      margin: 10px 0 0 10px;
    }
    .chooses {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 30px;
    }
    .isNear {
      margin: 20px 0;
      display: flex;
      justify-content: center;
      align-items: center;
      .title {
        color: rgba(3, 2, 2, 0.822);
      }
      .nearSwitch {
        display: flex;
        margin-left: 20px;
      }
    }
  }
  .content {
    margin-top: 41px;
    height: calc(100% - 41px);
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
  }
  .nologin {
    height: 100%;
    display: flex;
    align-items: center;
  }
}
</style>