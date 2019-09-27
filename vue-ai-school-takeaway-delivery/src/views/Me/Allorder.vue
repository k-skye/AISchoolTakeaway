<template>
  <div class="allorder">
    <div class="header">
      <van-nav-bar title="全部订单" left-arrow @click-left="$router.push('me')" />
    </div>
    <div class="contain">
      <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
        <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
        <van-collapse v-model="activeNames">
          <van-collapse-item name="1" icon="shop-o">
            <div slot="title" class="title">
              第四饭堂
              <van-icon name="arrow" class="icon" />C15
              <div class="end">
                <van-icon name="clock-o" class="icon" />08-25 12:24
              </div>
            </div>
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
                <div class="totalTitle">已得配送费：</div>
                <div class="totalPrice">¥2.0</div>
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
      loading: false,
      finished: false,
      list: [],
      activeNames: []
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