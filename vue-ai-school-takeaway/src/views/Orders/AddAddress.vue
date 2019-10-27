<template>
  <div class="addAddress">
    <div class="header">
      <van-nav-bar :title="title" left-arrow @click-left="$router.go(-1)" />
    </div>
    <!-- 添加地址 -->
    <div class="viewbody">
      <div class="content">
        <FormBlock
          label="联系人"
          placeholder="姓名"
          :tags="sexes"
          :sex="sex"
          @checkSex="checkSex"
          v-model="addressInfo.name"
        />
        <FormBlock v-model="addressInfo.phone" label="电话" placeholder="手机号码" />
        <FormBlock
          v-model="addressInfo.dormitory"
          @click="showPicker = true"
          label="地址"
          placeholder="选择宿舍"
          icon="angle-right"
        />
        <FormBlock
          v-model="addressInfo.roomNum"
          label="房间号"
          placeholder="xxx房"
          textarea="textarea"
        />
      </div>
      <!-- 确定按钮 -->
      <div class="form-button-wrap">
        <button @click="handleSave" class="form-button">确定</button>
      </div>
    </div>
    <!-- 搜索地址 -->
    <van-popup v-model="showPicker" position="bottom">
      <van-picker
        show-toolbar
        :columns="columns"
        @cancel="showPicker = false"
        @confirm="onConfirm"
      />
    </van-popup>
  </div>
</template>

<script>
import FormBlock from "../../components/Orders/FormBlock";
import { Toast } from "vant";
export default {
  name: "AddAddress",
  data() {
    return {
      title: "",
      sexes: ["先生", "女士"],
      addressInfo: {},
      sex: "",
      showPicker: false,
      columns: ['C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9', 'C10', 'C11', 'C12', 'C13', 'C14', 'C15', 'C16', 'C17', 'C18', 'C19', 'C20', 'C21', 'C22'],
      userInfo: null
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.addressInfo = to.params.addressInfo;
      vm.title = to.params.title;
      vm.userInfo = to.params.userInfo;
    });
  },
  methods: {
    onConfirm(value) {
      this.addressInfo.dormitory = value;
      this.showPicker = false;
    },
    checkSex(item) {
      if (item == "先生") {
        this.addressInfo.gender = "1";
        this.sex = "先生";
      } else {
        this.addressInfo.gender = "2";
        this.sex = "女士";
      }
    },
    handleSave() {
      if (!this.addressInfo.name) {
        this.showMsg("请输入联系人");
        return;
      }

      if (!this.addressInfo.phone) {
        this.showMsg("请输入手机号");
        return;
      }

      if (!this.addressInfo.dormitory || !this.addressInfo.roomNum) {
        this.showMsg("请输入收货地址");
        return;
      }

      // 存储数据
      if (this.title == "添加地址") {
        this.addAddress();
      } else {
        this.editAddress();
      }
    },
    showMsg(msg) {
      Toast(msg);
    },
    addAddress() {
      this.addressInfo.userID = this.userInfo.id;
      this.$axios
        .post(
          "https://takeawayapi.pykky.com/?s=Address.AddAddr",
          this.addressInfo
        )
        .then(res => {
          this.$store.dispatch("setAddrInfo", this.addressInfo);
          if (this.userInfo.mainAddressID == 0) {
            this.userInfo.mainAddressID == res.data.data;
            this.$store.dispatch("setUserInfo", this.userInfo);
          }
          this.$router.push("myAddress");
        })
        .catch(err => console.log(err));
    },
    editAddress() {
      this.$axios
        .post(
          "https://takeawayapi.pykky.com/?s=Address.ChangeAddr",
          this.addressInfo
        )
        .then(res => {
          this.$router.push("/myAddress");
        });
    }
  },
  components: {
    FormBlock
  }
};
</script>

<style scoped>
.addAddress {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
}
.viewbody {
  width: 100%;
  height: 100%;
  overflow: auto;
  box-sizing: border-box;
  background-color: #f5f5f5;
}

.content {
  padding-left: 4vw;
  background: #fff;
  font-size: 0.94rem;
}

/* 确定按钮 */
.form-button-wrap {
  padding: 5.333333vw 4vw;
  display: flex;
}
.form-button-wrap .form-button {
  background: #00d762;
  text-align: center;
  border-radius: 0.533333vw;
  flex: 1;
  font-size: 1.1rem;
  line-height: 5.066667vw;
  color: #fff;
  padding: 3.333333vw 0;
  border: none;
  font-weight: 500;
}
</style>
