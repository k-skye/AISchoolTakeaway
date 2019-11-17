<template>
  <div class="allorder">
    <div class="header">
      <van-nav-bar
        title="全部订单"
        left-arrow
        @click-left="$router.push('me')"
      />
    </div>
    <div class="contain">
      <van-list
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
          <van-collapse-item
            :name="index"
            icon="shop-o"
          >
            <div
              slot="title"
              class="title"
            >
              <span v-if="orderDelive.order.type==0">第{{ orderDelive.order.restNum }}饭堂</span>
              <span v-if="orderDelive.order.type==1">{{ orderDelive.order.expressAddr=='商业街京东派'?'商业街京东派':(orderDelive.order.expressAddr+'快递点') }}</span>
              <van-icon
                name="arrow"
                class="icon"
              />
              {{ orderDelive.dormitory }}{{ orderDelive.order.upstairs>0?'上楼':'' }}
              <div class="end">
                <van-icon
                  name="clock-o"
                  class="icon"
                />
                {{ orderDelive.delivedTime }}
              </div>
            </div>
            <div class="foods">
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
              <div
                v-if="orderDelive.order.type==0"
                class="totalMoney"
              >
                <div class="totalTitle">
                  商品总价：
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
                  已得配送费：
                </div>
                <div class="totalPrice">
                  ¥{{ parseFloat(orderDelive.order.deliveFee).toFixed(2) }}
                </div>
              </div>
            </div>
          </van-collapse-item>
        </van-collapse>
      </van-list>
    </div>
  </div>
</template>

<script>
export default {
  name: "Allorder",
  data() {
    return {
      orderlist: [], //存放当前订单容器
      orders: [], //存放所有订单容器
      offset: 1,
      size: 5,
      loading: false,
      finished: false,
      activeNames: [],
      deliverID: null,
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.deliverID = to.params.deliverID;
      vm.firstLoadData();
    });
  },
  methods: {
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
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Deliverorders.GetOneUserAllOrderFinish",
        {
          params: {
            deliverID: this.deliverID,
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
            "https://takeawayapi.pykky.com/?s=Deliverorders.GetOneUserAllOrderFinish",
            {
              params: {
                deliverID: this.deliverID,
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
    }
  }
};
</script>

<style lang="scss" scoped>
.allorder {
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
</style>