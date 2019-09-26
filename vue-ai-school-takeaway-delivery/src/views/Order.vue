<template>
  <div class="order">
    <div class="header">
      <div class="headerContain">
        <div class="orderButton">
          <van-switch :value="orderChecked" @input="onOrderInput" />
          <div class="headerTitle">{{orderChecked ? '接单:开' : '接单:关'}}</div>
        </div>
        <div class="headerEndButton">
          <van-icon :size="28" name="setting-o" @click="onSetting" />
        </div>
      </div>
    </div>
    <van-popup v-model="showRule" closeable position="bottom" :style="{ height: '50%' }">
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
          <Choose
            label="性别"
            :value="chooseSexValue"
            @getValue="getSexValue"
            placeholder="选择同学性别"
            :data="sexes"
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
    <div class="content">
      <van-list v-model="loading" :finished="finished" finished-text="没有更多了" @load="onLoad">
        <!-- <van-cell v-for="item in list" :key="item" :title="item" /> -->
        <van-collapse v-model="activeNames">
          <van-collapse-item name="1" :icon="statusIcon">
            <div slot="title" class="title">
              第四饭堂
              <van-icon name="arrow" class="icon" />C15
              <div class="end">
                <van-icon name="clock-o" class="icon" />12:24
                <van-icon name="bag-o" class="icon" />x4
                <van-icon name="contact" class="icon" />男
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
                <div class="totalTitle">可得配送费：</div>
                <div class="totalPrice">¥2.0</div>
              </div>
              <div class="orderThisButton">
                <van-button type="primary" @click="onOrderButtonClick">&nbsp接单&nbsp</van-button>
              </div>
            </div>
          </van-collapse-item>
        </van-collapse>
      </van-list>
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
import Choose from "../components/Choose";
import { Toast } from 'vant';
export default {
  name: "deliver",
  data() {
    return {
      orderChecked: false,
      loading: false,
      finished: false,
      list: [],
      showRule: false,
      chooseRestValue: "",
      rests: ["第一饭堂+第二饭堂", "第三饭堂", "第四饭堂", "门口"],
      addrs: [
        "c1",
        "c2",
        "c3",
        "c4",
        "c5",
        "c6",
        "c7",
        "c8",
        "c9",
        "c10",
        "c11",
        "c12",
        "c13",
        "c14",
        "c15",
        "c16",
        "c17",
        "c18",
        "c19",
        "c20",
        "c21",
        "c22",
        "c23"
      ],
      chooseAddrValue: "",
      nearChecked: true,
      chooseSexValue: "",
      sexes: ["男", "女"],
      activeNames: ["1"],
      statusIcon: "shop-o"
    };
  },
  methods: {
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
    onSetting() {
      this.showRule = true;
    },
    getRestValue(data) {
      this.chooseRestValue = data;
    },
    getAddrValue(data) {
      this.chooseAddrValue = data;
    },
    getSexValue(data) {
      this.chooseSexValue = data;
    },
    onNearInput() {
      this.nearChecked = !this.nearChecked;
    },
    onOrderButtonClick() {
      Dialog.confirm({
        title: "确定接单吗？",
        message: "注：如果接单后不送将受到相应处罚甚至封号！"
      }).then(() => {
        this.statusIcon = "checked";
        this.activeNames.forEach((item, index) => {
          if (item == "1") {
            this.activeNames.splice(index, 1);
          }
        });
        Toast.success('接单成功');
      });
    }
  },
  components: {
    Choose
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
      margin: 5px 0 0 10px;
    }
    .chooses {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 30px;
    }
    .isNear {
      margin: 10px 0;
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
}
</style>