<template>
  <div class="order">
    <div
      v-if="!firstlogin"
      class="header"
    >
      <div class="headerContain">
        <div class="orderButton">
          <div
            class="headerTitle"
            style="padding-right:10px;"
          >
            {{ orderChecked ? '接单提醒' : '接单提醒' }}
          </div>
          <van-switch
            :value="orderChecked"
            @input="onOrderInput"
          />
        </div>
        <div class="headerEndButton">
          <van-icon
            :size="28"
            name="setting-o"
            @click="onSetting"
          />
        </div>
      </div>
    </div>
    <van-popup
      v-if="!firstlogin"
      v-model="showRule"
      closeable
      position="bottom"
      :style="{ height: '50%' }"
    >
      <div class="settings">
        <div class="head">
          筛选
        </div>
        <div class="chooses">
          <Choose
            label="类型"
            :value="chooseTypeValue"
            placeholder="选择接单类型"
            :data="types"
            @getValue="getTypeValue"
          />
          <Choose
            label="快递点"
            :value="chooseExpressValue"
            placeholder="选择快递点"
            :data="expresss"
            @getValue="getExpressValue"
          />
          <Choose
            label="饭堂"
            :value="chooseRestValue"
            placeholder="选择饭堂"
            :data="rests"
            @getValue="getRestValue"
          />
          <Choose
            label="收货地址"
            :value="chooseAddrValue"
            placeholder="选择收货地址"
            :data="addrs"
            @getValue="getAddrValue"
          />
        </div>
        <div class="isNear">
          <div class="title">
            包括附近的宿舍
          </div>
          <div class="nearSwitch">
            <van-switch
              :value="nearChecked"
              @input="onNearInput"
            />
          </div>
        </div>
      </div>
    </van-popup>
    <div
      v-if="!firstlogin"
      class="content"
    >
      <van-pull-refresh
        v-model="isLoading"
        @refresh="onRefresh"
      >
        <van-list
          v-model="loading"
          :finished="finished"
          finished-text="没有更多了"
          @load="onLoad"
        >
          <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
          <van-collapse
            v-for="(order,index) in orders"
            :key="index"
            v-model="activeNames"
          >
            <van-collapse-item
              :name="index"
              :icon="handleSubmitData(order.haveSumit,order.type)"
            >
              <div
                slot="title"
                class="title"
              >
                <span v-if="order.type==0">
                  第{{ order.restNum }}饭堂
                </span>
                <span v-if="order.type==1">
                  {{ order.expressAddr=='商业街京东派'?'商业街京东派':(order.expressAddr+'快递点') }}
                </span>
                <van-icon
                  name="arrow"
                  class="icon"
                />
                {{ order.dormitory }}
                <van-tag
                  v-if="order.isNeedFast == 1"
                  round
                  type="danger"
                  style="margin-left:5px;text-align: center;"
                >
                  加急 +{{ order.fastMoney }} 元
                </van-tag>
                <div
                  v-if="order.upstairs != 0"
                  class="lou"
                >
                  要上楼
                </div>
                <div
                  v-if="order.type==0"
                  class="end"
                >
                  <van-icon
                    name="clock-o"
                    class="icon"
                  />
                  {{ order.shouldDeliveTime }}送达
                  <van-icon
                    name="bag-o"
                    class="icon"
                  />
                  x{{ order.foodsCount }}
                  <!-- <van-icon name="contact" class="icon" />男 -->
                </div>
                <div
                  v-if="order.type==1"
                  class="end"
                >
                  <van-icon
                    name="clock-o"
                    class="icon"
                  />
                  {{ order.shouldDeliveTime }}送达
                </div>
              </div>
              <div
                v-if="order.type==0"
                class="foods"
              >
                <ul>
                  <li
                    v-for="(food,indexFood) in order.foodsArr"
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
              <van-divider v-if="order.type==0" />
              <div class="bottom">
                <div
                  v-if="order.type==0"
                  class="totalMoney"
                >
                  <div class="totalTitle">
                    商品总价：
                  </div>
                  <div
                    class="totalPrice"
                  >
                    ¥{{ parseFloat(order.totalPrice).toFixed(2) - parseFloat(order.deliveFee).toFixed(2) }}
                  </div>
                </div>
                <div
                  v-if="order.type==1"
                  class="totalMoney"
                >
                  <div class="totalTitle">
                    重量：
                  </div>
                  <div
                    class="totalPrice"
                    style="color:#5a5a5a"
                  >
                    {{ handleWeightData(order.weight) }}
                  </div>
                </div>
                <div class="incomeMoney">
                  <div class="totalTitle">
                    可得配送费：
                  </div>
                  <div class="totalPrice">
                    ¥{{ parseFloat(order.deliveFee).toFixed(2) }}
                  </div>
                </div>
                <div class="orderThisButton">
                  <van-button
                    v-if="order.status == 2 ? true : false"
                    type="info"
                  >
                    已接单
                  </van-button>
                  <van-button
                    v-else
                    type="primary"
                    @click="onOrderButtonClick(order.id,index)"
                  >
                    接单
                  </van-button>
                </div>
              </div>
            </van-collapse-item>
          </van-collapse>
        </van-list>
      </van-pull-refresh>
    </div>
    <div
      v-else
      class="nologin"
    >
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
  name: "Deliver",
  components: {
    Choose,
    NoLoginInfo
  },
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
        "C22"
      ],
      chooseAddrValue: "",
      nearChecked: true,
      activeNames: [],
      firstlogin: false,
      isLoading: false,
      chooseRestNum: 0,
      chooseAddrNum: 0,
      chooseNearNum: 1,
      chooseTypeNum: 0,
      chooseExpressNum: 0,
      preUserInfo: null,
      chooseTypeValue: "",
      types: ["全部", "美食跑题", "快递代拿"],
      chooseExpressValue: "",
      expresss: ["全部","C3", "C4", "商业街京东派"],
    };
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.preUserInfo = to.params.preUserInfo;
      vm.getData();
    });
  },
  methods: {
    handleSubmitData(data,orderType){
      data = parseInt(data);
      switch (data) {
        case 1:
          return 'checked';
        default:
          orderType = parseInt(orderType);
          if (orderType == 1) {
            return 'send-gift-o';
          }else{
            return 'shop-o';
          }
      }
    },
    handleWeightData(weight){
      weight = parseInt(weight);
      switch (weight) {
        case 0:
          return '小(约0～3瓶中型怡宝)';
        case 1:
          return '中(约1瓶大型怡宝)';
        case 2:
          return '大(约1箱牛奶)';
        case 3:
          return '特大(约2箱牛奶)';
        case 4:
          return '>2箱牛奶重或者体积大';
        default:
          break;
      }
    },
    getExpressValue(data, index) {
      this.chooseExpressValue = data;
      this.chooseExpressNum = index;
      this.valueChange();
    },
    getTypeValue(data, index) {
      this.chooseTypeValue = data;
      this.chooseTypeNum = index;
      this.valueChange();
    },
    getData() {
      this.firstlogin = localStorage.firstlogin == 0 ? false : true;
      if (this.preUserInfo == null) {
        this.preUserInfo = this.userInfo;
      }
      this.chooseRestNum = this.preUserInfo.chooseRest;
      this.chooseAddrNum = this.preUserInfo.chooseAddr;
      this.chooseNearNum = this.preUserInfo.chooseNear;
      this.chooseTypeNum = this.preUserInfo.chooseType;
      this.chooseExpressNum = this.preUserInfo.chooseExpress;
      //开启提醒按钮
      this.orderChecked = this.preUserInfo.sendMessage == "1" ? true : false;
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
      var str = "";
      switch (this.chooseAddrNum) {
        case "0":
          this.chooseAddrValue = "全部";
          break;
        default:
          //其他数字-正则匹配
          str = "C" + this.chooseAddrNum;
          this.chooseAddrValue = str;
          break;
      }
      switch (this.chooseTypeNum) {
        case "0":
          this.chooseTypeValue = "全部";
          break;
        case "1":
          this.chooseTypeValue = "美食跑腿";
          break;
        case "2":
          this.chooseTypeValue = "快递代拿";
          break;
        default:
          break;
      }
      switch (this.chooseExpressNum) {
        case "0":
          this.chooseExpressValue = "全部";
          break;
        case "1":
          this.chooseExpressValue = "C3";
          break;
        case "2":
          this.chooseExpressValue = "C4";
          break;
        case "3":
          this.chooseExpressValue = "商业街京东派";
          break;
        default:
          break;
      }
      this.firstLoadData();
    },
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "http://tatestapi.pykky.com/?s=Orders.GetAllNeedDeliveOrders",
        {
          params: {
            deliverID: this.userInfo.id,
            page: this.offset,
          }
        }
      ).then(res => {
        if (JSON.stringify(res.data.data) == "{}") {
          this.finished = true;
          this.loading = false;
          return;
        }
        this.orderlist = res.data.data;
        this.handleData();
      });
    },
    handleData() {
      //对商品数据处理
      this.orderlist.forEach(order => {
        if (order.type == 0) {
          var OrderFoods = JSON.parse(order.foods);
          var foodsArr = new Array();
          OrderFoods.forEach(id => {
            order.food.forEach(food => {
              if (food.id == id) {
                foodsArr.push(food); //把每行food的全部数据对象都放入数组
              }
            });
          });
          order.foodsArr = foodsArr;
          order.foodsCount = foodsArr.length; //食物数量
        }
        this.orders.push(order);
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
        message: "开启之后如关闭本页面也会在微信里继续收到筛选后的接单提醒噢～"
      })
        .then(() => {
          this.orderChecked = orderChecked;
          //开始请求
          this.$axios(
            "http://tatestapi.pykky.com/?s=DeliverUsers.changeUserInfoOnSendMessage",
            {
              params: {
                userID: this.userInfo.id,
                sendMessage: this.orderChecked == true ? "1" : "0"
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              Toast.success("修改成功");
            } else {
              Toast.fail("失败！" + res.data.msg);
            }
          });
        })
        .catch(() => {
          // on cancel
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
            "http://tatestapi.pykky.com/?s=Orders.GetAllNeedDeliveOrders",
            {
              params: {
                deliverID: this.userInfo.id,
                page: this.offset,
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
    onSetting() {
      this.showRule = true;
    },
    getRestValue(data, index) {
      this.chooseRestValue = data;
      this.chooseRestNum = index;
      this.valueChange();
    },
    getAddrValue(data, index) {
      this.chooseAddrValue = data;
      this.chooseAddrNum = index;
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
        "http://tatestapi.pykky.com/?s=DeliverUsers.ChangeUserInfoOnChooseByUserId",
        {
          params: {
            userID: this.userInfo.id,
            chooseAddr: this.chooseAddrNum,
            chooseRest: this.chooseRestNum,
            chooseNear: this.chooseNearNum,
            chooseType: this.chooseTypeNum,
            chooseExpress: this.chooseExpressNum
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
      this.$axios("http://tatestapi.pykky.com/?s=DeliverUsers.GetUserInfo", {
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
        message: "注：如果接单后不送将受到相应处罚甚至封号！(加急单如送达不准时将退还加急费给用户)"
      })
        .then(() => {
          //开始创建订单
          this.$axios(
            "http://tatestapi.pykky.com/?s=Deliverorders.CreateOneOrder",
            {
              params: {
                orderID: orderID,
                deliverID: this.userInfo.id
              }
            }
          ).then(res => {
            if (res.data.data == "ok") {
              this.$set(this.orders[indexx], "haveSumit", 1); //让开头的图标变成打勾
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
      margin-left: 1px;
      display: flex;
      align-items: center;
      justify-content: flex-end; //只有一个元素的时候用，让设置按钮居右显示
      justify-content: space-between;
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
    .van-list {
      height: calc(100vh - 50px - 41px);
    }
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