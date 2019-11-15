<template>
  <div class="wallet">
    <!-- <van-popup v-model="showChangePass" closeable round :style="{ height: '40%',width: '80%' }">
      <div class="head">修改支付密码</div>
      <div class="contain">
        <div class="cells">
          <van-cell-group title=" ">
            <van-field v-model="oldpass" type="textarea" placeholder="请输入旧支付密码" rows="1" autosize></van-field>
            <van-field v-model="newpass" type="textarea" placeholder="请输入新支付密码" rows="1" autosize></van-field>
          </van-cell-group>
        </div>
        <div class="changeButton">
          <van-button slot="button" size="large" type="primary" @click="onChangePassButtonClick">修改</van-button>
        </div>
      </div>
    </van-popup>-->
    <div class="header">
      <van-nav-bar
        title="钱包"
        left-arrow
        @click-left="$router.push('me')"
      />
    </div>
    <div class="showMoney">
      <div class="totalMoney">
        ¥ {{ userInfo.noun }}
        <br>
        <span>含收入¥ {{ userInfo.income }}</span>
      </div>
      <div class="payButton">
        <van-button
          type="primary"
          @click="showPopup"
        >
          提现到零钱
        </van-button>
      </div>
    </div>
    <van-popup
      v-model="show"
      closeable
      :style="{ height: '60%',width: '80%' }"
    >
      <div class="popup">
        <p class="header">
          提现
        </p>
        <p class="head">
          提现到微信零钱
        </p>
        <p
          class="total"
        >
          ¥ {{ (parseFloat(userInfo.noun) - (parseFloat(userInfo.income) * 0.3)).toFixed(2) }}
        </p>
        <van-divider />
        <div class="foodPay">
          <span class="left">商品原价</span>
          <span
            class="right"
          >¥ {{ (parseFloat(userInfo.noun) - parseFloat(userInfo.income)).toFixed(2) }}</span>
        </div>
        <div class="income">
          <span class="left">实际收入</span>
          <span class="right">¥ {{ userInfo.income }}</span>
        </div>
        <div class="outcome">
          <span
            class="left"
            @click="clickLeftInfo"
          >
            服务费 
            <van-icon name="info-o" />
          </span>
          <span class="right">- ¥ {{ (parseFloat(userInfo.income) * 0.3).toFixed(2) }}</span>
        </div>
        <div class="yesButton">
          <van-button
            type="primary"
            size="large"
            @click="onCashButtonClick"
          >
            提现
          </van-button>
        </div>
      </div>
    </van-popup>
    <div class="cells">
      <van-cell-group title=" ">
        <van-cell
          icon="gold-coin-o"
          title="待确认收货"
          :value="'¥'+((parseFloat(userInfo.notDoneMoney)).toFixed(2))"
        />
        <van-cell
          icon="newspaper-o"
          title="账单明细"
          is-link
          to="billdetail"
        />
        <van-cell
          icon="balance-o"
          title="月收入"
          :value="'¥'+((parseFloat(userInfo.thisMonthMoney)).toFixed(2))"
        />
        <!-- <van-cell icon="gold-coin-o" title="修改支付密码" is-link @click="onChangePassCellClick" /> -->
      </van-cell-group>
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
export default {
  name: "Me",
  data() {
    return {
      show: false,
      finallyMoney: 0
    };
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  methods: {
    showPopup() {
      this.show = true;
    },
    onCashButtonClick() {
      this.show = false;
      //开始提现
      this.$axios
        .post("http://tatestapi.pykky.com/?s=Tradinglog.CashOneUser", {
          deliverID: this.userInfo.id
        })
        .then(res => {
          //微信支付
          if (res.data.data == "ok") {
            //Toast.success("提现成功"); //微信还没接口之前的解决方案
            Dialog.alert({
              title: "成功",
              message: "已提交提现申请！但因为我们是新平台注册未满一个月，微信官方还不允许我们直接转账到你的零钱，所以麻烦加下客服微信：17889465893，发你的名字来提现吧！"
            });
            //刷新userInfo
            const openid = localStorage.openid;
            //用openid去get全部用户信息回来
            this.$axios(
              "http://tatestapi.pykky.com/?s=DeliverUsers.GetUserInfo",
              {
                params: {
                  openid: openid
                }
              }
            ).then(res => {
              this.$store.dispatch("setUserInfo", res.data.data);
            });
          } else {
            Dialog.alert({
              title: "提现失败",
              message: res.data.msg
            });
          }
        });
    },
    clickLeftInfo() {
      Dialog.alert({
        message: "服务费是从伙伴的实际收入(配送费)中收取30%作为我们的运营成本"
      });
    }
    /*     onChangePassCellClick() {
      this.showChangePass = true;
    },
    onChangePassButtonClick(){
      this.showChangePass = false;
      Toast.success("修改成功");
      this.oldpass = "";
      this.newpass = "";
    } */
  }
};
</script>

<style lang="scss" scoped>
.wallet {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .head {
    display: flex;
    justify-content: center;
    margin-top: 18px;
  }
  .contain {
    .cells {
      margin-top: 5px;
    }
    .changeButton {
      display: flex;
      justify-content: center;
      margin: 18px 10px 0;
    }
  }

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
        margin-top: 3px;
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
    .yesButton {
      margin-top: 10px;
      width: 90%;
    }
  }
}
</style>