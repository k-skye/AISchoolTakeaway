<template>
  <div class="order">
    <div class="order-card-body" v-for="(order,index) in orders" :key="index">
      <div class="order-card-wrap" @click="toOrderInfo(order)" v-if="order">
        <img :src="'https://takeaway.pykky.com/restImgs/'+order.restLogo" alt />
        <div class="order-card-content">
          <div class="order-card-head">
            <div class="title">
              <a>
                <span>{{order.restName}}</span>
                <i class="fa fa-angle-right"></i>
              </a>
              <p>订单已完成</p>
            </div>
            <p class="date-time">{{order.createTime}}</p>
          </div>
          <div class="order-card-detail">
            <p class="detail">{{order.showfood}}</p>
            <p class="price">¥{{order.payPrice}}</p>
          </div>
        </div>
      </div>
      <div class="order-card-bottom">
        <button class="cardbutton" @click="$router.push('/shop')">再来一单</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "order",
  data() {
    return {
      orderlist: [], //存放当前订单容器
      offset: 1,
      size: 5,
      allLoaded: false,
      loading: false,
      orders: [] //存放所有订单容器
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getData();
    });
  },
  methods: {
    getData() {
      this.firstLoadData();
    },
    firstLoadData() {
      this.offset = 1;
      this.allLoaded = false;
      // 拉取商家信息
      this.$axios("https://takeawayapi.pykky.com/?s=Orders.GetOnesAllOrders", {
        params: {
          userID: this.userInfo.id,
          offset: this.offset,
          limit: this.size
        }
      }).then(res => {
        if (res.data.data.length == 0) {
          // TODO
          this.allLoaded = true;
          return;
        }
        this.orderlist = res.data.data;
        this.orders = res.data.data;
        this.handleData();
      });
    },
    loadMore() {
      // TODO
      // 异步更新数据
      setTimeout(() => {
        if (!this.allLoaded) {
          this.offset += 5;
          // 拉取商家信息
          this.$axios(
            "https://takeawayapi.pykky.com/?s=Orders.GetOnesAllOrders",
            {
              params: {
                userID: this.userInfo.id,
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
                this.allLoaded = true;
                this.loading = false;
              }
            } else {
              // 数据为空
              this.allLoaded = true;
              this.loading = false;
            }
          });
        }
      }, 500);
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
        var o = 0;
        foodsArr.forEach(food => {
          if (o < 2) {
            showfood += " " + food.name;
          }
          o++;
        });
        if (o > 2) {
          showfood += " 等";
        }
        this.orders[i].showfood = showfood;
        i++;
      });
    },
    toOrderInfo(order) {
      var toData = {};
      toData.restName = order.restName;
      toData.createTime = order.createTime;
      toData.selectFoods = order.foodsArr;
      toData.payPrice = order.payPrice;
      //切记传值前一定要先建立对应属性为null！！！！！
      toData.deliveryFee = null;
      toData.value = null;
      //店铺配送费信息
      //TODO 订单增加配送费信息
      this.$axios("https://takeawayapi.pykky.com/?s=Restaurant.GetOneRest", {
        params: {
          id: order.restID
        }
      }).then(res => {
        toData.deliveryFee = res.data.data.deliveryFee;
      });
      //收货地址信息
      this.$axios("https://takeawayapi.pykky.com/?s=Address.GetOneAddr", {
        params: {
          id: order.addressID
        }
      }).then(res => {
        toData.addrInfo = res.data.data;
      });
      //红包信息
      if (order.discountID != -1) {
        this.$axios(
          "https://takeawayapi.pykky.com/?s=Discount.GetOnesDiscounts",
          {
            params: {
              id: order.discountID
            }
          }
        ).then(res => {
          toData.value = res.data.data.value;
        });
      } else {
        toData.value = "0";
      }
      //订单备注信息
      toData.remark = order.remark;
      this.$router.push({ name: "orderInfo", params: toData });
    }
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  }
};
</script>

<style scoped>
.order {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  margin-bottom: 2.666667vw;
}
.order-card-body {
  margin-top: 2.666667vw;
  background-color: #fff;
  padding: 3.733333vw 0 0 4vw;
}
.order-card-wrap {
  display: flex;
}
.order-card-wrap > img {
  height: 8.533333vw;
  border-radius: 0.533333vw;
  border: 1px solid #eee;
  width: 8.533333vw;
  margin-right: 2.666667vw;
}
.order-card-content {
  flex: 1;
}
.order-card-head {
  border-bottom: 1px solid #eee;
  padding-right: 3.466667vw;
  padding-bottom: 2.666667vw;
}
.order-card-head .title {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.order-card-head .title > a {
  font-size: 1rem;
  line-height: 1.5em;
  color: #333;
  text-decoration: none;
}
.order-card-head .title > a > span {
  display: inline-block;
  max-width: 10em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.order-card-head .title > a > i {
  margin-left: 1.333333vw;
  color: #ccc;
  vertical-align: super;
}
.order-card-head .title > p {
  font-size: 0.8rem;
  text-align: right;
  color: #333;
  max-width: 14em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.date-time {
  font-size: 0.6rem;
  color: #999;
}
.order-card-detail {
  display: flex;
  justify-content: space-between;
  padding: 4vw 3.466667vw 4vw 0;
  font-size: 0.8rem;
}
.order-card-detail .detail {
  color: #666;
  display: flex;
  align-items: center;
}
.order-card-detail .price {
  flex-basis: 16vw;
  text-align: right;
  color: #333;
  font-weight: 700;
}
.order-card-bottom {
  display: flex;
  border-top: 1px solid #eee;
  padding: 2.666667vw 4.266667vw;
  justify-content: flex-end;
}
.cardbutton {
  padding: 1.333333vw 2.666667vw;
  border: 1px solid #2395ff;
  border-radius: 0.533333vw;
  background-color: transparent;
  outline: none;
  font-size: 0.8rem;
  color: #2395ff;
  margin-left: 2vw;
}
</style>
