<template>
  <div class="me">
    <div class="headInfo">
      <div class="head-img">
        <img :src="logoImgUrl" />
      </div>
      <div class="head-profile">
        <p v-if="firstlogin" class="user-id" @click="handleRes">登陆/注册</p>
        <p class="user-id" v-else>{{userInfo.name}}</p>
        <p class="user-phone">
          <i class="fa fa-mobile"></i>
          <span v-if="firstlogin">登陆后就能开始点餐咯～</span>
          <span v-else>{{encryptPhone(userInfo.phoneNo)}}</span>
        </p>
      </div>
      <!-- <van-icon name="arrow" /> 
      TODO: 后期加改头像和昵称功能
      -->
    </div>
    <div v-if="!firstlogin">
      <van-cell-group title=" ">
        <van-cell
          icon="star-o"
          is-link
          @click="$router.push({name:'allcomment',params: { userID: userInfo.id }})"
        >
          <template slot="title">
            <span class="custom-title">评价</span>
          </template>
        </van-cell>
        <van-cell icon="location-o" title="地址" is-link @click="myAddress" />
        <van-cell icon="after-sale" is-link @click="discountButtonClick">
          <template slot="title">
            <span class="custom-title">红包</span>
          </template>
        </van-cell>
      </van-cell-group>
      <van-cell-group title=" ">
        <van-cell icon="question-o" title="帮助" is-link to="help" />
        <van-cell icon="notes-o" title="协议" is-link to="protocol" />
        <van-cell
          icon="bullhorn-o"
          title="建议反馈"
          is-link
          @click="$router.push({name:'support',params: { userID: userInfo.id }})"
        />
        <van-cell icon="service-o" title="联系客服" is-link @click="serviceClick" />
      </van-cell-group>
    </div>
    <van-popup v-model="showList" position="bottom">
      <van-coupon-list
        :show-exchange-bar="false"
        :coupons="coupons"
        :disabled-coupons="disabledCoupons"
        @change="onChange"
        close-button-text="关闭"
      />
    </van-popup>
    <div class="nologin" v-if="firstlogin">
      <NoLoginInfo />
    </div>
  </div>
</template>

<script>
import { Dialog } from "vant";
import NoLoginInfo from "../components/NoLoginInfo";
export default {
  name: "me",
  data() {
    return {
      firstlogin: true,
      logoImgUrl: "",
      showList: false,
      coupons: [],
      disabledCoupons: []
    };
  },
  created() {
    this.firstlogin = localStorage.firstlogin == 0 ? false : true;
    this.logoImgUrl =
      this.firstlogin == false
        ? this.userInfo.avatar
        : "http://takeawaydeliver.pykky.com/newICON.png";
  },
  methods: {
    serviceClick() {
      Dialog({
        message: "请加平台客服微信：17889465893，如紧急可拨打此电话！"
      });
    },
    onChange() {
      this.showList = false;
    },
    discountButtonClick() {
      this.showList = true;
      //拿红包数据
      this.$axios(
        "https://takeawayapi.pykky.com/?s=Discount.GetOnesAllcounts",
        {
          params: {
            userID: this.userInfo.id
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
    },
    handleRes() {
      const appid = "wx3df92dead7bcd174";
      const redirectUrl = encodeURI(
        "https://takeawayapi.pykky.com/?s=Users.getInfoInWechat"
      );
      const wechatUrl =
        "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" +
        appid +
        "&redirect_uri=" +
        redirectUrl +
        "&response_type=code&scope=snsapi_userinfo&state=register#wechat_redirect";
      window.location.href = wechatUrl;
    },
    encryptPhone(phone) {
      return phone.replace(/(\d{3})\d{4}(\d{4})/, "$1****$2");
    },
    myAddress() {
      if (this.userInfo.mainAddressID > 0) {
        this.$router.push("/myAddress");
      } else {
        this.$router.push({
          name: "addAddress",
          params: {
            title: "添加地址",
            addressInfo: {
              name: "",
              sex: "",
              phone: "",
              address: "",
              bottom: "",
              tag: ""
            },
            userInfo: this.userInfo
          }
        });
      }
    }
  },
  computed: {
    userInfo() {
      return this.$store.getters.userInfo;
    }
  },
  components: {
    NoLoginInfo
  }
};
</script>

<style lang="scss" scoped>
.me {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .nologin {
    height: 70%;
    display: flex;
    align-items: center;
  }
  .headInfo {
    display: flex;
    // background-image: linear-gradient(90deg, #0af, #0085ff);
    background-color: white;
    padding: 6.666667vw 4vw;
    //color: #fff;
    color: black;
    align-items: center;
    .head-img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
      }
    }
    .head-profile {
      overflow: hidden;
      margin-left: 4.8vw;
      flex-grow: 1;
      .user-id {
        max-width: 40vw;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 1.3rem;
        margin-bottom: 2.133333vw;
        font-weight: 700;
        display: flex;
        align-items: center;
        i {
          font-size: 1.2rem;
        }
      }
      .user-phone {
        display: flex;
        align-items: center;
        font-size: 0.8rem;
        i {
          margin-right: 0.666667vw;
          font-size: 1rem;
        }
      }
    }
  }
}
</style>