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
        <div class="chooses"><!-- 做居中处理 -->
        <div class="chooseRest">
          <van-field
            readonly
            clickable
            label="饭堂"
            :value="chooseRestValue"
            placeholder="选择饭堂"
            @click="showChooseRest = true"
          />

          <van-popup v-model="showChooseRest" position="bottom">
            <van-picker
              show-toolbar
              :columns="rests"
              @cancel="showChooseRest = false"
              @confirm="onChooseRestConfirm"
            />
          </van-popup>
        </div>
        <div class="chooseUserAddr">
          <van-field
            readonly
            clickable
            label="收货地址"
            :value="chooseAddrValue"
            placeholder="选择收货地址"
            @click="showChooseAddr = true"
          />

          <van-popup v-model="showChooseAddr" position="bottom">
            <van-picker
              show-toolbar
              :columns="addrs"
              @cancel="showChooseAddr = false"
              @confirm="onChooseAddrConfirm"
            />
          </van-popup>
        </div>
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
        <van-cell v-for="item in list" :key="item" :title="item" />
      </van-list>
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
export default {
  name: "deliver",
  data() {
    return {
      orderChecked: false,
      loading: false,
      finished: false,
      list: [],
      showRule: false,
      showChooseRest: false,
      chooseRestValue: "",
      rests: ["第一饭堂", "第二饭堂", "第三饭堂", "第四饭堂", "门口"],
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
      showChooseAddr: false,
      chooseAddrValue: "",
      nearChecked: true
    };
  },
  methods: {
    onOrderInput(orderChecked) {
      Dialog.confirm({
        title: "提醒",
        message: "是否切换开关？"
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
    onChooseRestConfirm(value) {
      this.chooseRestValue = value;
      this.showChooseRest = false;
    },
    onChooseAddrConfirm(value) {
      this.chooseAddrValue = value;
      this.showChooseAddr = false;
    },
    onNearInput() {
      this.nearChecked = !this.nearChecked;
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
    .chooseRest {
      margin: 20px 30px 0 30px;
    }
    .chooseUserAddr {
      margin: 5px 30px 0 30px;
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
  }
}
</style>