<template>
  <div class="login">
    <van-popup
      v-model="showPicker"
      position="bottom"
    >
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
    <!-- 真实姓名 -->
    <InputGroup
      v-model="realName"
      type="text"
      placeholder="真实姓名"
      :error="errors.realName"
    />
    <!-- 性别 -->
    <div
      class="sex"
      @click="showPicker = true"
    >
      <InputGroup
        :noinput="true"
        type="text"
        :placeholder="placeholdSex"
        :error="errors.sex"
      />
    </div>
    <div class="upload">
      学生证：
      <!--       <van-image
        width="100px"
        height="70px"
        fit="fill"
        :src="imgUrl"
        v-show="imgUrl == '' ? false : true"
      /> -->
      <!-- <vueOssUploader
        :debug="false"
        path="/deliverCard/"
        v-on:success="uploaded"
        @error="showError"
      ></vueOssUploader> -->
      <van-uploader
        v-model="imgUrlList"
        :max-count="1"
        :after-read="afterRead"
      />
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
import { Toast } from 'vant';
const OSS = require("ali-oss");
/* import axioskkk from "axios"; */
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
      openid: "",
      sex: "",
      placeholdSex: "性别",
      realName: "",
      showPicker: false,
      columns: ["男", "女"],
/*       uploadedUrl: "",
      imgUrl: "", */
      needUploadedUrls: [],
      imgUrlList: [],
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
        this.needUploadedUrls == []
      ) {
        return true;
      } else return false;
    }
  },
  created() {
    this.openid = this.getQueryVariable("openid");
  },
  methods: {
    /* uploaded(res) {
      this.uploadedUrl = res.ossPath;
      this.imgUrl = res.ossUrl;
      Toast('上传成功');
    },
    showError(e) {
      this.errors = {
        sex: '图片上传失败' + e
      };
    }, */
    afterRead(file) {
      const toastLoading = Toast.loading({
        duration: 0,
        message: "上传中...",
        forbidClick: true
      });
      // 此时可以自行将文件上传至服务器
      //file分成两部分 一个是base64后的内容 一个是file对象。这里用file对象即可
      let client = new OSS({
        accessKeyId: "LTAI4FxYGKep93a9uktmMKKK",
        accessKeySecret: "R0aVpyguKOlY0AI1D8h2dpNZxvcYDJ",
        endpoint: "oss-cn-shenzhen.aliyuncs.com", //阿里云oss的新东西
        bucket: "takeawayschool",
        secure: true
      });
      let date = new Date();
      let name =
        "deliverCard/" +
        date.getFullYear() +
        (date.getMonth() + 1) +
        date.getDate() +
        date.getHours() +
        date.getMinutes() +
        date.getSeconds() +
        date.getMilliseconds() +
        "." +
        file.file.name.split(".").pop();
      client
        .put(name, file.file)
        .then(() => {
          this.needUploadedUrls.push(name);
          toastLoading.clear();
          Toast.success("上传成功");
        })
        .catch(err => {
          Toast.fail("上传图片失败！" + err);
          this.errors = {
              sex: "图片上传失败，请重试！"
            };
        });
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
          cardImg: JSON.stringify(this.needUploadedUrls)
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
