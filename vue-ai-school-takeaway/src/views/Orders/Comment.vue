<template>
  <div class="comment">
    <div class="header">
      <van-nav-bar
        title="评价"
        left-arrow
        @click-left="$router.push('order')"
      />
    </div>
    <van-cell-group title=" ">
      <van-field
        v-model="message"
        rows="2"
        autosize
        clearable
        type="textarea"
        maxlength="50"
        placeholder="请输入评价"
        show-word-limit
      />
      <div class="rate">
        评分：
        <van-rate v-model="value" />
      </div>
      <div class="upload">
        图片：
        <van-uploader
          v-model="imgUrlList"
          :max-count="3"
          :after-read="afterRead"
        />
      </div>
    </van-cell-group>
    <div class="button">
      <van-button
        type="primary"
        size="large"
        @click="handleClick"
      >
        提交
      </van-button>
    </div>
  </div>
</template>

<script>
import { Toast } from "vant";
const OSS = require("ali-oss");
export default {
  name: "Comment",
  data() {
    return {
      message: "",
      value: 5,
      needUploadedUrls: [],
      imgUrlList: [],
      userID: null,
      orderID: null,
      restID: null
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.userID = to.params.userID;
      vm.orderID = to.params.orderID;
      vm.restID = to.params.restID;
    });
  },
  methods: {
    handleClick() {
      this.$axios
        .post("http://tatestapi.pykky.com/?s=Comment.CommentByUser", {
          userID: this.userID,
          text: this.message,
          restID: this.restID,
          orderID: this.orderID,
          images: JSON.stringify(this.needUploadedUrls),
          stars: this.value
        })
        .then(() => {
          /*           if (res.data.data == "ok") { */
          Toast.success("评论成功");
          this.$router.push("order");
          /*           } else {
            Toast.fail("评论失败！" + res.data.msg);
          } */
        });
    },
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
        "commentImg/" +
        this.userID +
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
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.comment {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  .rate {
    color: #323233;
    padding: 15px;
    padding-bottom: 10px;
    display: flex;
    align-items: center;
  }
  .upload {
    color: #323233;
    padding: 15px;
    display: flex;
    align-items: center;
  }
  .button {
    margin: 10px;
  }
}
</style>