<template>
  <div class="allorder">
    <div class="header">
      <van-nav-bar title="全部订单" left-arrow @click-left="$router.push('me')" />
    </div>
    <div class="contain">
      <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
        <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
        <van-collapse v-model="activeNames" v-for="(orderDelive,index) in orders" :key="index">
          <van-collapse-item :name="index" icon="shop-o">
            <div slot="title" class="title">
              第{{orderDelive.order.restNum}}饭堂
              <van-icon name="arrow" class="icon" />
              {{orderDelive.dormitory}}
              <div class="end">
                <van-icon name="clock-o" class="icon" />
                {{orderDelive.delivedTime}}
              </div>
            </div>
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
                <div class="totalTitle">已得配送费：</div>
                <div class="totalPrice">¥{{parseFloat(orderDelive.order.deliveFee).toFixed(2)}}</div>
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
  name: "allorder",
  data() {
    return {
      orderlist: [], //存放当前订单容器
      orders: [], //存放所有订单容器
      offset: 1,
      size: 5,
      loading: false,
      finished: false,
      activeNames: [],
      deliverID: null
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.deliverID = to.params.deliverID;
      vm.firstLoadData();
    });
  },
  methods: {
    firstLoadData() {
      this.offset = 1;
      this.finished = false;
      // 拉取商家信息
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Deliverorders.GetOneUserAllOrderFinish",
        {
          params: {
            deliverID: this.deliverID,
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
            "https://takeawayapi.pykky.com/?s=Deliverorders.GetOneUserAllOrderFinish",
            {
              params: {
                deliverID: this.deliverID,
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