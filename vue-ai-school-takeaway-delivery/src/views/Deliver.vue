<template>
  <div class="deliver">
    <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
      <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
      <van-collapse v-model="activeNames">
        <van-collapse-item name="1" >
          <div slot="title" class="title">
            <van-tag v-if="status == 0 ? true : false" type="danger">待取</van-tag>
            <van-tag v-if="status == 1 ? true : false" type="primary">待送达</van-tag>
            &nbsp&nbsp第四饭堂
            <van-icon name="arrow" class="icon" />C15
            <div class="end">
              <van-icon name="clock-o" class="icon" />12:24
            </div>
          </div>
          <div class="info">
            <div class="user">
              <div class="usernameAndPhone">
                <div class="username">
                  <van-icon name="manager" class="icon" />
                  <div class="name">吕家建</div>
                </div>
                <div class="userphone">
                  <van-icon name="phone" class="icon" />
                  <div class="phoneno">18477658767</div>
                </div>
              </div>
              <div class="notes">
                <van-icon name="comment" class="icon" />
                <div class="note">备注：无</div>
              </div>
            </div>
            <div class="restAndAddr">
              <div class="rest">
                <van-icon name="shop" class="icon" />
                <div class="res">第四饭堂 铁板炒饭店</div>
              </div>
              <div class="address">
                <van-icon name="location" class="icon" />
                <div class="addr">C15 670</div>
              </div>
            </div>
          </div>
          <van-divider />
          <div class="foods">
            <ul>
              <li>
                <div class="food">
                  <div class="name">1.招牌手撕鸡</div>
                  <div class="price">¥14.00</div>
                </div>
              </li>
              <li>
                <div class="food">
                  <div class="name">2.湿炒河粉</div>
                  <div class="price">¥18.00</div>
                </div>
              </li>
            </ul>
          </div>
          <van-divider />
          <div class="bottom">
            <div class="totalMoney">
              <div class="totalTitle">商品总价：</div>
              <div class="totalPrice">¥32.00</div>
            </div>
            <div class="incomeMoney">
              <div class="totalTitle">可得配送费：</div>
              <div class="totalPrice">¥2.0</div>
            </div>
            <div class="orderThisButton" v-if="status == 0 ? true : false">
              <van-button type="info" @click="onGetGoodsButtonClick">已取到商品</van-button>
            </div>
            <div class="deliveButton" v-if="status == 1 ? true : false">
              <van-button type="primary" @click="onDeliveButtonClick">我已送达</van-button>
            </div>
          </div>
        </van-collapse-item>
      </van-collapse>
    </van-list>
  </div>
</template>

<script>
import { Dialog } from "vant";
import { Toast } from "vant";
export default {
  name: "deliver",
  data() {
    return {
      loading: false,
      finished: false,
      list: [],
      activeNames: [],
      status: 0
    };
  },
  methods: {
    onLoad() {
      // 异步更新数据
      setTimeout(() => {
        for (let i = 0; i < 10; i++) {
          this.list.push(this.list.length + 1);
        }
        // 加载状态结束
        this.loading = false;

        // 数据全部加载完成
        if (this.list.length >= 40) {
          this.finished = true;
        }
      }, 500);
    },
    onGetGoodsButtonClick() {
      Dialog.confirm({
        title: "确定取到商品了吗？"
      }).then(() => {
        this.status = 1;
        this.activeNames.forEach((item, index) => {
          if (item == "1") {
            this.activeNames.splice(index, 1);
          }
        });
        Toast.success("成功");
      });
    },
    onDeliveButtonClick(){
      Dialog.confirm({
        title: "确定已送达到客户手上了吗？"
      }).then(() => {
        //移除这个订单在这个页面
        Toast.success("成功");
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
}
</style>