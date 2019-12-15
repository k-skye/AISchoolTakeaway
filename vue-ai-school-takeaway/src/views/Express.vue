<template>
  <div class="express">
    <van-popup
      v-model="showTimePicker"
      position="bottom"
    >
      <van-picker
        show-toolbar
        :columns="columns"
        @cancel="showTimePicker = false"
        @confirm="onTimeConfirm"
      />
    </van-popup>
    <div
      v-if="!firstlogin"
      class="view-body"
    >
      <!-- 收货地址 -->
      <section class="cart-address">
        <p class="title">
          您的快递配送至
        </p>
        <p class="address-detail">
          <span
            v-if="addrInfo"
            @click="$router.push('/myAddress')"
          >{{ addrInfo.dormitory }} {{ addrInfo.roomNum }}</span>
          <span v-else>
            <span
              v-if="haveAddress"
              @click="$router.push('/myAddress')"
            >选择收货地址</span>
            <span
              v-else
              @click="addAddress"
            >新增收货地址</span>
          </span>
          <i class="fa fa-angle-right" />
        </p>
        <h2
          v-if="addrInfo"
          class="address-name"
        >
          <span>{{ addrInfo.name }}</span>
          <span v-if="addrInfo.gender">({{ addrInfo.gender == 1 ? '先生' : '小姐' }})</span>
          <span class="phone">{{ addrInfo.phone }}</span>
        </h2>
      </section>

      <!-- 送达时间 -->
      <section class="checkout-section">
        <div
          class="delivery"
          @click="shouldShowPicker"
        >
          <div class="deliver-left">
            <span class="delivery-time">送达时间</span>
          </div>
          <div class="delivery-right">
            <span class="delivery-select">{{ (!okTime)?'填写重量后自动计算':okTime }}</span>
          </div>
        </div>
        <!-- 是否加急 -->
        <div class="deliveryNeedFast">
          <div class="deliver-left">
            <span class="delivery-time">加急</span>
          </div>
          <div class="delivery-right">
            <van-radio-group
              v-model="radio"
              style="display:flex;"
              @change="radioChange"
            >
              <van-radio
                name="1"
                style="padding-right:10px;"
              >
                否
              </van-radio>
              <van-radio name="2">
                是
              </van-radio>
            </van-radio-group>
          </div>
        </div>
      </section>

      <van-cell-group>
        <!-- <van-field :error-message="MessageTextFieldError" v-model="autoMessageText" autosize clearable type="textarea" placeholder="请把快递的短信内容粘贴到这里" rows="1">
          <van-button slot="button" type="primary">自动填写</van-button>
        </van-field>
        以后可能用到
        -->
        <van-field
          v-show="parseInt(radio)==2"
          readonly
          clickable
          label="指定时间"
          :value="currentTime"
          placeholder="在此时间之前伙伴必须送达"
          :error-message="timeError"
          @click="showCriticalTimePicker = true"
        />
        <van-popup
          v-model="showCriticalTimePicker"
          position="bottom"
        >
          <van-datetime-picker
            v-model="currentTime"
            type="time"
            :min-hour="nowhour"
            :max-hour="24"
            :filter="filter"
            @cancel="showCriticalTimePicker = false"
            @confirm="onCriticalTimeConfirm"
          />
        </van-popup>
        <van-field
          v-show="parseInt(radio)==2"
          v-model="customGift"
          center
          clearable
          type="number"
          maxlength="4"
          label="悬赏红包"
          placeholder="红包金额(越大接单几率增大)"
          :error-message="giftError"
          @input="customGiftInput"
        >
          <div
            slot="button"
            style="display:flex;align-items: center;"
            @click="showGiftQuestion"
          >
            元
            <van-icon
              style="padding-left:5px;"
              name="question-o"
            />
          </div>
        </van-field>

        <van-field
          readonly
          clickable
          label="取件地址"
          :value="addrvalue"
          placeholder="请选择地址"
          :error-message="addrError"
          @click="showPicker = true"
        />
        <van-popup
          v-model="showPicker"
          position="bottom"
        >
          <van-picker
            show-toolbar
            :columns="addrcolumns"
            @cancel="showPicker = false"
            @confirm="onAddrConfirm"
          />
        </van-popup>
        <van-field
          v-model="code"
          clearable
          label="取件码"
          placeholder="请输入取件码"
          :error-message="codeError"
        />
        <van-field
          readonly
          clickable
          label="重量"
          :value="weightvalue"
          placeholder="请选择重量"
          :error-message="weightError"
          @click="showWeightPicker = true"
        />
        <van-popup
          v-model="showWeightPicker"
          position="bottom"
        >
          <van-picker
            show-toolbar
            :columns="weightcolumns"
            @cancel="showWeightPicker = false"
            @confirm="onWeightConfirm"
          />
        </van-popup>
        <van-field
          v-show="showCustomFee"
          v-model="customFee"
          center
          clearable
          label="伙伴费"
          type="number"
          maxlength="3"
          placeholder="自行决定伙伴费(2元起)"
          :error-message="customFeeError"
          @input="customFeeInput"
        >
          <div
            slot="button"
            style="display:flex;align-items: center;"
            @click="showCustomQuestion"
          >
            元
            <van-icon
              style="padding-left:5px;"
              name="question-o"
            />
          </div>
        </van-field>
        <van-field
          readonly
          clickable
          label="类型"
          :value="goodTypevalue"
          placeholder="请选择类型"
          :error-message="goodTypeError"
          right-icon="question-o"
          @focus="showgoodTypePicker = true"
          @click-right-icon="goodTypeQuestion"
        />
        <van-popup
          v-model="showgoodTypePicker"
          position="bottom"
        >
          <van-picker
            show-toolbar
            :columns="goodTypecolumns"
            @cancel="showgoodTypePicker = false"
            @confirm="ongoodTypeConfirm"
          />
        </van-popup>
        <van-field
          v-model="remark"
          rows="2"
          label="备注"
          type="textarea"
          maxlength="80"
          placeholder="请输入备注，建议把快递短信内容直接复制到这里"
          show-word-limit
          clearable
          :error-message="markError"
        />
      </van-cell-group>
      <van-cell-group style="margin-top:2.133333vw;">
        <van-coupon-cell
          title="红包"
          :coupons="coupons"
          :chosen-coupon="chosenCoupon"
          @click="showList = true"
        />
        <!-- 优惠券列表 -->
        <van-popup
          v-model="showList"
          position="bottom"
        >
          <van-coupon-list
            :show-exchange-bar="false"
            :coupons="coupons"
            :chosen-coupon="chosenCoupon"
            :disabled-coupons="disabledCoupons"
            @change="onChange"
          />
        </van-popup>
      </van-cell-group>
      <van-submit-bar
        :price="okFee*100"
        button-text="微信支付"
        button-type="primary"
        style="margin-bottom:50px"
        safe-area-inset-bottom
        :loading="isPayloading"
        @submit="handlePay"
      >
        <div
          class="info"
          style="display:flex"
          @click="payQuestion"
        >
          <van-icon
            name="info-o"
            color="gray"
            style="margin-left:10px"
          />注意事项
        </div>
      </van-submit-bar>
    </div>

    <div
      v-if="firstlogin"
      class="nologin"
    >
      <NoLoginInfo />
    </div>
  </div>
</template>

<script>
import NoLoginInfo from "../components/NoLoginInfo";
// eslint-disable-next-line no-unused-vars
import { Toast } from "vant";
import { Dialog } from "vant";
export default {
  name: "Express",
  components: {
    NoLoginInfo
  },
  data() {
    return {
      firstlogin: false,
      haveAddress: false,
      addrInfo: null,
      okTime: "",
      deliveFee: 2.0,
      okFee: 0.0,
      deliverTime: 30,
      goodsAddress: "",
      goodsAddressError: "",
      code: "",
      codeError: "",
      weightError: "",
      addrvalue: "",
      weightvalue: "",
      showPicker: false,
      showTimePicker: false,
      showWeightPicker: false,
      showCriticalTimePicker: false,
      addrcolumns: ["C3", "C4", "商业街京东派"],
      weightcolumns: [
        "小：<1.5kg 约3瓶中型怡宝",
        "中：>1.5-3kg 约1瓶大型怡宝",
        "大：>3kg-5kg 约1箱牛奶",
        "特大：>5kg-10kg 约2箱牛奶",
        "其他：>10kg/体积过大"
      ],
      columns: [],
      currentTime: "",
      remark: "",
      radio: "1",
      addrError: "",
      markError: "",
      timeError: "",
      nowhour: 0,
      customFee: "",
      customGift: "",
      showCustomFee: false,
      goodTypevalue: "",
      goodTypeError: "",
      showgoodTypePicker: false,
      goodTypecolumns: [
        "衣物",
        "饮品",
        "食物",
        "书籍",
        "个人护理",
        "数码电器",
        "其他"
      ],
      giftError: "",
      weightNum: 0,
      customFeeError: "",
      preuserInfo: null,
      isPayloading: false,
      chosenCoupon: -1,
      coupons: [],
      disabledCoupons: [],
      showList: false,
      noDiscountPrice: 0.0,
      Discounid: -1,
      DiscountPrice: 0.0
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.getData();
    });
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  methods: {
    onChange(index) {
      this.showList = false;
      this.chosenCoupon = index;
      if (this.chosenCoupon == -1) {
        this.okFee = this.noDiscountPrice;
        this.Discounid = -1;
        this.radioChange();
        return;
      }
      this.Discounid = this.coupons[index].id;
      this.DiscountPrice = (this.coupons[index].value)/100;
      this.okFee = this.noDiscountPrice - this.DiscountPrice;
      this.okTime = "21:00前送达";
    },
    onTimeConfirm(okTime) {
      if (this.chosenCoupon == -1) {
        this.okTime = okTime;
      }else{
        this.okTime = "21:00前送达";
      }
      this.showTimePicker = false;
    },
    goodTypeQuestion() {
      this.showgoodTypePicker = false;
      Dialog({
        message:
          "仅用于在与伙伴有纠纷时使用，我们会保护您隐私，类型信息伙伴将无法看到"
      });
    },
    payQuestion() {
      Dialog({
        message:
          "注意：重量计费遵循“少补”的规则，即如果伙伴实际拿到的快递与您设置的重量不符，您将需要额外支付3元的尾款"
      });
    },
    ongoodTypeConfirm(type) {
      this.goodTypevalue = type;
      this.showgoodTypePicker = false;
    },
    showGiftQuestion() {
      Dialog({
        message:
          "在原配送费的基础上给伙伴的加急红包，注意：金额少可能会无人接单，届时将自动全额退款，可能会浪费您的等待时间！伙伴接单后送达超时5分钟也将把红包全额退回！"
      });
    },
    showCustomQuestion() {
      Dialog({ message: "可自己决定想要给伙伴多少配送费，越多接单几率越大。" });
    },
    customGiftInput(value) {
      if (value != "") {
        this.okFee = this.deliveFee + parseFloat(value);
        this.noDiscountPrice = this.okFee;
      }
    },
    customFeeInput(value) {
      let gift = 0.0;
      if (this.customGift != "") {
        gift = parseInt(this.customGift);
      }
      if (value != "") {
        this.okFee = parseFloat(value) + gift;
        this.noDiscountPrice = this.okFee;
      }
    },
    onWeightConfirm(weightChoose, index) {
      this.weightvalue = weightChoose;
      let gift = 0.0;
      if (this.customGift != "") {
        gift = parseInt(this.customGift);
      }
      this.columns=[];
      switch (index) {
        case 1:
          this.calcData(15);
          /* this.deliveFee = 5;
          this.okFee = this.deliveFee + gift;
          this.showCustomFee = false; */
          this.deliveFee = 0;
          this.showCustomFee = true;
          break;
        case 2:
          this.calcData(30);
          /* this.deliveFee = 7;
          this.okFee = this.deliveFee + gift;
          this.showCustomFee = false; */
          this.deliveFee = 0;
          this.showCustomFee = true;
          break;
        case 3:
          this.calcData(45);
          /* this.deliveFee = 9;
          this.okFee = this.deliveFee + gift;
          this.showCustomFee = false; */
          this.deliveFee = 0;
          this.showCustomFee = true;
          break;
        case 4:
          this.calcData(60);
          this.deliveFee = 0;
          this.showCustomFee = true;
          break;
        default:
          this.calcData(0);
          this.deliveFee = 2; //test:0.1
          this.okFee = this.deliveFee + gift;
          this.showCustomFee = false;
          break;
      }
      this.weightNum = index;
      this.showWeightPicker = false;
      this.noDiscountPrice = this.okFee;
      this.onChange(this.chosenCoupon);
    },
    shouldShowPicker() {
      if (parseInt(this.radio) != 2) {
        this.showTimePicker = true;
      } else {
        this.showTimePicker = false;
      }
    },
    deliveryTime(time) {
      let date = new Date();
      time = parseInt(time);
      date.setMinutes(date.getMinutes() + time);
      let gethour = date.getHours();
      if (gethour < 10) {
        gethour = "0" + gethour;
      }
      let getmin = date.getMinutes();
      if (getmin < 10) {
        getmin = "0" + getmin;
      }
      return gethour + ":" + getmin;
    },
    onCriticalTimeConfirm(time) {
      this.currentTime = time;
      this.showCriticalTimePicker = false;
    },
    filter(type, options) {
      if (type === "minute") {
        return options.filter(option => option % 5 === 0);
      }

      return options;
    },
    handlePay() {
      let time = new Date();
      if (((time.getHours())>=21) || ((time.getHours())<=6)) {
        Dialog({ message: '平台不在服务时间内，服务时间:07点-20点' });
        return;
      }
      //先判断所有需要填的空是否已填写
      this.addrError = "";
      this.codeError = "";
      this.weightError = "";
      this.goodTypeError = "";
      this.timeError = "";
      this.giftError = "";
      this.customFeeError = "";
      if (!this.addrInfo) {
        Toast("请先选择收货地址");
        return;
      }
      if (!this.addrvalue) {
        this.addrError = "请选择取件地址";
        return;
      }
      if (!this.code) {
        this.codeError = "取件码不能为空";
        return;
      }
      if (!this.weightvalue) {
        this.weightError = "请选择重量";
        return;
      }
      if (!this.goodTypevalue) {
        this.goodTypeError = "快递类型不能为空";
        return;
      }
      if (!this.remark) {
        this.remark = "无";
      }
      if (parseInt(this.radio) == 2) {
        if (!this.currentTime) {
          this.timeError = "请选择指定时间";
          return;
        }
        if (!this.customGift) {
          this.giftError = "红包金额不能为空";
          return;
        }
        if (parseFloat(this.customGift) == 0) {
          this.giftError = "红包金额必须大于0";
          return;
        }
        this.okTime = this.currentTime;
      } else {
        this.customGift = "0";
      }
      if (this.weightNum >= 1) {
        //特殊其他件
        if (!this.customFee) {
          this.customFeeError = "伙伴配送费不能为空";
          return;
        }
        if (parseFloat(this.customFee) < 2) {
          this.customFeeError = "伙伴配送费不能低于2元";
          return;
        }
      }
      //开始创建订单
      this.isPayloading = true;
      //正则取时间
      const finalTime = this.okTime
        .match(/\d+:\d+/g)
        .join("")
        .substring(0);
      let nowDate = new Date();
      if (this.okTime.search("明天") != -1) {
        //有明天
        nowDate.setDate(nowDate.getDate() + 1);
      }
      const finalFormatTime =
        this.dateFormat("YYYY-mm-dd ", nowDate) + finalTime;
      let isNeedFastNum = parseInt(this.radio) - 1;
      this.$axios
        .post("https://takeawayapi.pykky.com/?s=Orders.CreateOneExpressOrder", {
          userID: this.userInfo.id,
          remark: this.remark,
          expressAddr: this.addrvalue,
          totalPrice: (this.okFee+this.DiscountPrice),
          payPrice: this.okFee,
          addrID: this.addrInfo.id,
          openID: this.userInfo.openid,
          shouldDeliveTime: finalFormatTime,
          deliveFee: (this.okFee+this.DiscountPrice),
          expressCode: this.code,
          weight: this.weightNum,
          goodType: this.goodTypevalue,
          isNeedFast: isNeedFastNum,
          fastMoney: this.customGift,
          discountID: this.Discounid
        })
        .then(res => {
          //重要接口加错误处理！！！
          if (res.data.msg == "") {
            //微信支付
            const data = JSON.parse(res.data.data);
            // eslint-disable-next-line no-undef
            WeixinJSBridge.invoke("getBrandWCPayRequest", data, res => {
              if (res.err_msg == "get_brand_wcpay_request:ok") {
                // 使用以上方式判断前端返回,微信团队郑重提示：
                //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                Toast.success("支付成功");
                this.$router.push("/order");
              } else {
                Dialog({
                  message:
                    "支付失败：" +
                    res.data.data.err_code +
                    res.data.data.err_desc +
                    res.data.data.err_msg
                });
              }
              this.isPayloading = false;
            });
          } else {
            Dialog({
              message: "创建订单失败：" + res.data.msg
            });
          }
        });
    },
    radioChange() {
      let nowtime = new Date();
      let nowHour = this.dateFormat("H", nowtime);
      let gift = 0.0;
      if (this.customGift != "") {
        gift = parseInt(this.customGift);
      }
      this.customGift = "";
      switch (parseInt(this.radio)) {
        case 2:
          this.okTime = "指定时间之前必达";
          //计算指定时间列表
          this.nowhour = parseInt(nowHour);
          //this.okFee = this.deliveFee + 1;
          break;
        default:
          if (this.chosenCoupon == -1) {
            this.okTime =
            "尽快送达 (" + this.deliveryTime(this.deliverTime) + "送达)";
          }else{
            this.okTime = "21:00前送达";
          }
          this.okFee = this.okFee - gift;
          this.noDiscountPrice = this.okFee;
          //this.okFee = this.deliveFee;
          break;
      }
    },
    onAddrConfirm(addr) {
      this.addrvalue = addr;
      this.showPicker = false;
    },
    dateFormat(fmt, date) {
      let ret;
      let opt = {
        "Y+": date.getFullYear().toString(), // 年
        "m+": (date.getMonth() + 1).toString(), // 月
        "d+": date.getDate().toString(), // 日
        "H+": date.getHours().toString(), // 时
        "M+": date.getMinutes().toString(), // 分
        "S+": date.getSeconds().toString() // 秒
        // 有其他格式化字符需求可以继续添加，必须转化成字符串
      };
      for (let k in opt) {
        ret = new RegExp("(" + k + ")").exec(fmt);
        if (ret) {
          fmt = fmt.replace(
            ret[1],
            ret[1].length == 1 ? opt[k] : opt[k].padStart(ret[1].length, "0")
          );
        }
      }
      return fmt;
    },
    calcData(initTime) {
      //动态计算预估时间和配送费
      const roomNum = 1;
      let dormitory = this.addrInfo.dormitory;
      if (dormitory) {
        dormitory = dormitory.replace(/[^0-9]/gi, "");
      }
      //两个之间差值来算，把宿舍分成2个区域
      const dormNum = dormitory >= 1 && dormitory <= 6 ? 1 : 2;
      //默认起始配送费和时间为
      this.deliveFee = 2;
      this.deliverTime = 30 + initTime;
      //区域点餐
      switch (dormNum) {
        case 1: //第一个区域点餐
          switch (roomNum) {
            case 3:
              //this.deliveFee += 0.5;
              this.deliverTime += 15;
              break;
            case 4:
              //this.deliveFee += 1;
              this.deliverTime += 30;
              break;
            default:
              break;
          }
          break;
        case 2: //第二个区域点餐
          switch (roomNum) {
            case 1:
              //this.deliveFee += 1;
              this.deliverTime += 30;
              break;
            case 2:
              //this.deliveFee += 1;
              this.deliverTime += 30;
              break;
            default:
              break;
          }
          break;
        default:
          break;
      }
      //时间处理
      this.columns.push(
        "尽快送达 (" + this.deliveryTime(this.deliverTime) + "送达)"
      );
      this.okTime =
        "尽快送达 (" + this.deliveryTime(this.deliverTime) + "送达)";
      //现在距离明天24点还有多少小时
      let nowtime = new Date();
      let future = new Date();
      future.setHours(0, 0, 0, 0);
      future.setDate(future.getDate() + 1);
      let times = future.getTime() - nowtime.getTime();
      let halfhour = parseInt((times / 1000 / 60 / 60) % 24);
      for (
        let i = this.deliverTime * 2;
        i <= halfhour * 2 * 30 + 48 * 30;
        i += 30
      ) {
        if (i > halfhour * 2 * 30) {
          this.columns.push("明天 " + this.deliveryTime(i));
        } else {
          this.columns.push(this.deliveryTime(i));
        }
      }
      this.okFee = this.deliveFee;
      this.noDiscountPrice = this.okFee;
    },
    getData() {
      this.firstlogin = localStorage.firstlogin == 0 ? false : true;
      //拿收货地址
      this.addrInfo = this.$store.getters.addrInfo;
      //用openid去get全部用户信息回来
      const openid = localStorage.openid;
      this.$axios("https://takeawayapi.pykky.com/?s=Users.GetUserInfo", {
        params: {
          openid: openid
        }
      }).then(res => {
        this.preuserInfo = res.data.data;
        if (this.preuserInfo.mainAddressID != 0) {
          this.haveAddress = true;
          if (!this.addrInfo) {
            this.$axios("https://takeawayapi.pykky.com/?s=Address.GetOneAddr", {
              params: {
                id: this.preuserInfo.mainAddressID
              }
            }).then(res => {
              this.addrInfo = res.data.data;
              this.$store.dispatch("setAddrInfo", this.addrInfo);
              //this.calcData();
            });
          } else {
            //this.calcData();
          }
        } else {
          this.haveAddress = false;
        }
        //拿用户红包
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Discount.GetOnesAllcounts",
        {
          params: {
            userID: this.preuserInfo.id,
            type: 2
          }
        }
      ).then(res => {
        if (res.data.data != 0) {
          res.data.data.forEach(element => {
            element.forEach(item => {
              item.condition = item.conditions;
            });
          });
          this.disabledCoupons = res.data.data[0];
          this.coupons = res.data.data[1];
        }
      });
      });
    },
    addAddress() {
      this.$router.push({
        name: "addAddress",
        params: {
          title: "添加地址提交",
          userInfo: this.userInfo,
          addressInfo: {
            name: "",
            sex: "",
            phone: "",
            address: "",
            bottom: ""
          }
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.express {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .nologin {
    height: 100%;
    display: flex;
    align-items: center;
  }
  .view-body {
    width: 100%;
    box-sizing: border-box;
    font-size: 0.9rem;
    color: #333;
    padding-bottom: 18.133333vw;
    padding-left: 1.6vw;
    padding-right: 1.6vw;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    .cart-address {
      background-color: transparent;
      padding: 4.266667vw 2.133333vw 2.933333vw 2.133333vw;
      min-height: 22.133333vw;
      color: black;
      overflow: hidden;
      background-color: white;
      margin-top: 1.6vw;
      padding-left: 5.133333vw;
      .title {
        font-size: 0.9rem;
        line-height: 1.21;
        color: gray;
      }
      .address-detail {
        font-size: 1.3rem;
        font-weight: 600;
        line-height: 1.88;
        overflow: hidden;
        display: flex;
        align-items: center;
      }
      span {
        /* display: inline-block; */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: calc(100% - 4vw);
      }
      i {
        margin-left: 2.133333vw;
      }
      .address-name {
        font-size: 0.86rem;
        line-height: 1.21;
        margin-bottom: 1.333333vw;
        .phone {
          margin-left: 2.133333vw;
        }
      }
    }
    .checkout-section {
      margin-bottom: 2.133333vw;
      padding: 0 5.333333vw;
      background: #fff;
      box-shadow: 0 0.133333vw 0.266667vw 0 rgba(0, 0, 0, 0.05);
    }
    .delivery {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px dotted #eee;
      padding: 4.266667vw 0;
    }
    .deliveryNeedFast {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1vw 0 4.266667vw 0;
    }
    .delivery-left {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .delivery-time {
      font-size: 1rem;
      font-weight: 500;
    }
    .delivery-right {
      text-align: right;
      display: flex;
      align-items: center;
    }
    .delivery-select {
      text-align: right;
      color: #2395ff;
    }
    .delivery-right > i {
      margin-left: 0.666667vw;
      color: #888;
      font-size: 1.2rem;
    }
  }
}
</style>
