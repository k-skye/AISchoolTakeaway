<template>
  <div class="login">
    <div class="logo">
      <img
        src="../assets/logo.png"
        alt="my login image"
      >
    </div>
    <!--  学号 -->
    <InputGroup
      v-model="stuID"
      type="number"
      placeholder="学号"
      :error="errors.stuid"
    />
    <!-- 手机号 -->
    <InputGroup
      v-model="phone"
      type="number"
      placeholder="手机号"
      :btn-title="btnTitle"
      :disabled="disabled"
      :error="errors.phone"
      @btnClick="getVerifyCode"
    />
    <!-- 验证码 -->
    <InputGroup
      v-model="verifyCode"
      type="number"
      placeholder="验证码"
      :error="errors.code"
    />
    <!-- 用户服务协议 -->
    <div class="login_des">
      <p>
        注册即表示您已同意
        <span @click="$router.push('regprotocol')">《用户服务协议》</span>
      </p>
    </div>
    <!-- 登录按钮 -->
    <div class="login_btn">
      <button
        :disabled="isClick"
        @click="handleLogin"
      >
        注册
      </button>
    </div>
  </div>
</template>

<script>
import InputGroup from "../components/InputGroup";
export default {
  name: "Login",
  components: {
    InputGroup
  },
  data() {
    return {
      phone: "",
      verifyCode: "",
      errors: {},
      btnTitle: "获取验证码",
      disabled: false,
      codeID: "", //验证码ID
      stuID: "",
      openid: ""
    };
  },
  computed: {
    isClick() {
      if (!this.phone || !this.verifyCode || !this.stuID) {
        return true;
      } else return false;
    }
  },
  created() {
    this.openid = this.getQueryVariable("openid");
  },
  methods: {
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
        .post("http://123.207.230.132:1203/?s=Users.UserReg", {
          phoneNo: this.phone,
          loginCode: this.verifyCode,
          codeID: this.codeID,
          stuID: this.stuID,
          openid: this.openid
        })
        .then(res => {
          // 检验成功 设置登录状态并且跳转到/
          if (res.data.ret === 200) {
            localStorage.setItem("openid", this.openid);
            localStorage.setItem("firstlogin", 0);
            this.$router.push("/");
          }
        })
        .catch(err => {
          // 返回错误信息
          this.errors = {
            code: err.response.data.msg
          };
        });
    },
    getVerifyCode() {
      if (this.validatePhone()) {
        this.validateBtn();
        // 发送网络请求
        this.$axios
          .post("http://123.207.230.132:1203/?s=Users.SendMessage", {
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
    validatePhone() {
      // 验证手机号码
      if (!this.phone) {
        this.errors = {
          phone: "手机号码不能为空"
        };
        return false;
      } else if (!/^1[345678]\d{9}$/.test(this.phone)) {
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
    }
  }
};
</script>

<style scoped lang="scss">
.login {
  width: 100%;
  height: 100%;
  padding: 0 30px;
  box-sizing: border-box;
  background: #fff;
  .logo {
    text-align: center;
    img {
      width: 200px;
      margin-bottom: -15px;
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
}
</style>
