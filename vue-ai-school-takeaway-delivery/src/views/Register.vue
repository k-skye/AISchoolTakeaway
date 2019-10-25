<template>
  <div class="login">
    <van-popup v-model="showPicker" position="bottom">
      <van-picker
        show-toolbar
        :columns="columns"
        @cancel="showPicker = false"
        @confirm="onConfirm"
      />
    </van-popup>
    <!-- <div class="logo">
      <img src="../assets/logo.png" alt="my login image" />
    </div>-->
    <!--  学号 -->
    <InputGroup type="number" v-model="stuID" placeholder="学号" :error="errors.stuid" />
    <!-- 手机号 -->
    <InputGroup
      type="number"
      v-model="phone"
      placeholder="手机号"
      :btnTitle="btnTitle"
      :disabled="disabled"
      :error="errors.phone"
      @btnClick="getVerifyCode"
    />
    <!-- 验证码 -->
    <InputGroup type="number" v-model="verifyCode" placeholder="验证码" :error="errors.code" />
    <!-- 真实姓名 -->
    <InputGroup type="text" v-model="realName" placeholder="真实姓名" :error="errors.realName" />
    <!-- 性别 -->
    <div class="sex" @click="showPicker = true">
      <InputGroup :noinput="true" type="text" :placeholder="placeholdSex" :error="errors.sex" />
    </div>
    <div class="upload">
      学生证：
      <van-image
        width="100px"
        height="70px"
        fit="fill"
        :src="imgUrl"
        v-show="imgUrl == '' ? false : true"
      />
      <vueOssUploader
        :debug="false"
        path="/deliverCard/"
        v-on:success="uploaded"
        @error="showError"
      ></vueOssUploader>
    </div>
    <!-- 用户服务协议 -->
    <div class="login_des">
      <p>
        注册即表示您已同意
        <span @click="$router.push('regprotocol')">《用户服务协议》</span>
      </p>
    </div>
    <!-- 登录按钮 -->
    <div class="login_btn">
      <button :disabled="isClick" @click="handleLogin">注册</button>
    </div>
  </div>
</template>

<script>
import InputGroup from "../components/InputGroup";
import { Toast } from 'vant';
/* import axioskkk from "axios"; */
export default {
  name: "login",
  data() {
    return {
      phone: "",
      verifyCode: "",
      errors: {},
      btnTitle: "获取验证码",
      disabled: false,
      codeID: "", //验证码ID
      stuID: "",
      openid: "",
      sex: "",
      placeholdSex: "性别",
      realName: "",
      showPicker: false,
      columns: ["男", "女"],
      uploadedUrl: "",
      imgUrl: ""
    };
  },
  computed: {
    isClick() {
      if (
        !this.phone ||
        !this.verifyCode ||
        !this.stuID ||
        !this.realName ||
        !this.sex ||
        !this.uploadedUrl
      ) {
        return true;
      } else return false;
    }
  },
  created() {
    this.openid = this.getQueryVariable("openid");
  },
  methods: {
    uploaded(res) {
      this.uploadedUrl = res.ossPath;
      this.imgUrl = res.ossUrl;
      Toast('上传成功');
    },
    showError(e) {
      this.errors = {
        sex: '图片上传失败' + e
      };
    },
    getQueryVariable(variable) {
      var query = window.location.search.substring(1);
      var vars = query.split("&");
      for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        if (pair[0] == variable) {
          return pair[1];
        }
      }
      return false;
    },
    handleLogin() {
      // 取消错误提醒
      this.errors = {};
      // 发送请求
      this.$axios
        .post("https://takeawayapi.pykky.com/?s=DeliverUsers.UserReg", {
          phoneNo: this.phone,
          loginCode: this.verifyCode,
          codeID: this.codeID,
          stuID: this.stuID,
          openid: this.openid,
          realName: this.realName,
          sex: this.sex,
          cardImg: this.uploadedUrl
        })
        .then(res => {
          // 检验成功 设置登录状态并且跳转到/
          if (res.data.ret === 200) {
            localStorage.setItem("openid", this.openid);
            localStorage.setItem("firstlogin", 0);
            this.$router.push("me");
          } else {
            // 返回错误信息
            this.errors = {
              code: res.data.msg
            };
          }
        });
    },
    getVerifyCode() {
      if (this.validatePhone()) {
        this.validateBtn();
        // 发送网络请求
        this.$axios
          .post("https://takeawayapi.pykky.com/?s=DeliverUsers.SendMessage", {
            phoneNo: this.phone
          })
          .then(res => {
            if (res.data.ret == 402) {
              this.errors = {
                phone: "手机号码已存在,请用原来绑定的微信登陆"
              };
            } else {
              this.codeID = res.data.data.codeID;
            }
          });
      }
    },
    validateBtn() {
      let time = 60;
      let timer = setInterval(() => {
        if (time == 0) {
          clearInterval(timer);
          this.btnTitle = "获取验证码";
          this.disabled = false;
        } else {
          // 倒计时
          this.btnTitle = time + "秒后重试";
          this.disabled = true;
          time--;
        }
      }, 1000);
    },
    onConfirm(value) {
      this.sex = value == "男" ? 1 : 2;
      this.placeholdSex = this.sex == 1 ? "男" : "女";
      this.showPicker = false;
    },
    validatePhone() {
      // 验证手机号码
      if (!this.phone) {
        this.errors = {
          phone: "手机号码不能为空"
        };
        return false;
      } else if (!/^1[3456789]\d{9}$/.test(this.phone)) {
        this.errors = {
          phone: "请填写正确的手机号码"
        };
        return false;
      } else if (!/\b\d{12}\b/.test(this.stuID)) {
        this.errors = {
          stuid: "请先填写正确的学号"
        };
        return false;
      } else {
        this.errors = {};
        return true;
      }
    },
/*     afterRead(e) {
      // 此时可以自行将文件上传至服务器
      let fd = new FormData();
      fd.append("file", e.file);
      axioskkk
        .post("https://takeawayapi.pykky.com/?s=UploadImg.DeliverCard", fd, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        })
        .then(res => {
          if (res.data.data.code != 1) {
            this.errors = {
              sex: "图片上传失败，请重试！"
            };
          } else {
            this.fileList[0] = { url: res.data.data.url };
          }
        });
    } */
  },
  components: {
    InputGroup
  }
};
</script>

<style scoped lang="scss">
.login {
  width: 100%;
  height: 100%;
  padding: 10px 30px;
  box-sizing: border-box;
  background: #fff;
  overflow: auto;
  .logo {
    text-align: center;
    img {
      width: 100px;
    }
  }
  .text_group,
  .login_des,
  .login_btn {
    margin-top: 20px;
    button {
      width: 100%;
      height: 40px;
      background-color: #48ca38;
      border-radius: 4px;
      color: white;
      font-size: 14px;
      border: none;
      outline: none;
    }
    button[disabled] {
      background-color: #8bda81;
    }
  }
  .login_des {
    color: #aaa;
    line-height: 22px;
    text-align: center;
    span {
      color: #4d90fe;
    }
  }
  .upload {
    margin-top: 20px;
    padding-left: 8px;
    display: flex;
    align-items: center;
    color: #323233;
  }
}
</style>
