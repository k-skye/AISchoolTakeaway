<template>
  <div class="wallet">
    <div class="header">
      <van-nav-bar title="钱包" left-arrow @click-left="$router.push('me')" />
    </div>
    <div class="showMoney">
      <div class="totalMoney">
        ¥ 104.00
        <br />
        <span>收入¥ 26.00</span>
      </div>
      <div class="payButton">
        <van-button type="primary" @click="showPopup">提现到零钱</van-button>
      </div>
    </div>
    <van-popup v-model="show" closeable :style="{ height: '90%',width: '80%' }">
      <div class="popup">
        <p class="header">请输入支付密码</p>
        <p class="head">提现到微信零钱</p>
        <p class="total">¥ 98.80</p>
        <van-divider />
        <div class="foodPay">
          <span class="left">商品原价</span>
          <span class="right">¥ 78.00</span>
        </div>
        <div class="income">
          <span class="left">实际收入</span>
          <span class="right">¥ 26.00</span>
        </div>
        <div class="outcome">
          <span class="left" @click="clickLeftInfo">
            服务费&nbsp
            <van-icon name="info-o" />
          </span>
          <span class="right">- ¥ 5.20</span>
        </div>
        <div class="password">
          <van-password-input
            :value="value"
            info="密码为 6 位数字"
            :focused="showKeyboard"
            @focus="showKeyboard = true"
          />
        </div>
        <van-number-keyboard
          :show="showKeyboard"
          @input="onInput"
          @delete="onDelete"
          @blur="showKeyboard = false"
        />
      </div>
    </van-popup>
    <div class="cells">
      <van-cell-group title=" ">
        <van-cell icon="newspaper-o" title="账单明细" is-link to="wallet" />
        <van-cell icon="gold-coin-o" title="修改支付密码" is-link to="wallet" />
      </van-cell-group>
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
export default {
  name: "me",
  data() {
    return {
      show: false,
      value: "",
      showKeyboard: true
    };
  },
  methods: {
    showPopup() {
      this.show = true;
    },
    onInput(key) {
      this.value = (this.value + key).slice(0, 6);
    },
    onDelete() {
      this.value = this.value.slice(0, this.value.length - 1);
    },
    clickLeftInfo() {
      Dialog.alert({
        message: "服务费是从伙伴的实际收入中收取20%作为我们的运营成本"
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.wallet {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .showMoney {
    background-color: white;
    margin-top: 30px;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    .totalMoney {
      font-size: 40px;
      span {
        display: flex;
        margin-top: 1px;
        font-size: 15px;
        color: gray;
      }
    }
  }
  .popup {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;

    .header {
      width: 100%;
      display: flex;
      justify-content: center;
      font-size: 25px;
      margin-top: 13px;
    }
    .head {
      width: 100%;
      display: flex;
      justify-content: center;
      font-size: 15px;
      margin-top: 30px;
    }
    .total {
      width: 100%;
      display: flex;
      justify-content: center;
      font-size: 50px;
      margin-top: 13px;
    }
    .van-divider {
      width: 100%;
    }
    .foodPay {
      display: flex;
      justify-content: space-between;
      width: 100%;
      padding: 0 15px;
      .left {
        color: gray;
      }
    }
    .income {
      display: flex;
      justify-content: space-between;
      width: 100%;
      margin-top: 10px;
      padding: 0 15px;
      .left {
        color: gray;
      }
    }
    .outcome {
      display: flex;
      justify-content: space-between;
      width: 100%;
      padding: 10px 15px;
      .left {
        color: gray;
        display: flex;
      }
    }
    .password {
      width: 100vw;
      margin-top: 5px;
    }
  }
}
</style>