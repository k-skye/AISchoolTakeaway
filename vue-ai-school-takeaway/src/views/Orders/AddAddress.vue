<template>
  <div class="addAddress">
    <Header :isLeft="true" :title="title" />
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
          @click="showSearch=true"
          label="地址"
          placeholder="选择宿舍"
          icon="angle-right"
        />
        <FormBlock
          v-model="addressInfo.roomNum"
          label="房间号"
          placeholder="404房"
          textarea="textarea"
        />
      </div>
      <!-- 确定按钮 -->
      <div class="form-button-wrap">
        <button @click="handleSave" class="form-button">确定</button>
      </div>
    </div>
    <!-- 搜索地址 -->
    <AddressSearch @close="showSearch=false" :showSearch="showSearch" :addressInfo="addressInfo" />
  </div>
</template>

<script>
import Header from "../../components/Header";
import FormBlock from "../../components/Orders/FormBlock";
import AddressSearch from "../../components/Orders/AddressSearch";
import { Toast } from "mint-ui";
export default {
  name: "AddAddress",
  data() {
    return {
      title: "",
      sexes: ["先生", "女士"],
      addressInfo: {},
      showSearch: false,
      sex: "",
      userID: ""
    };
  },
  beforeRouteEnter(to, from, next) {
    next(vm => {
      vm.addressInfo = to.params.addressInfo;
      vm.title = to.params.title;
      vm.userID = to.params.userID;
    });
  },
  methods: {
    checkSex(item) {
      if (item == "先生") {
        this.addressInfo.gender = "1";
        this.sex = "先生";
      } else {
        this.addressInfo.gender = "2";
        this.sex = "女士";
      }
      this.addressInfo.dormitory = "c15";
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
      Toast({
        message: msg,
        position: "bottom",
        duration: 2000
      });
    },
    addAddress() {
      this.addressInfo.userID = this.userID;
      this.$axios
        .post("/api/?s=Address.AddAddr", this.addressInfo)
        .then(res => {
          if (!this.$store.getters.userInfo) {
            this.$store.dispatch("setAddrInfo", this.addressInfo);
          }
          this.$router.push("myAddress");
        })
        .catch(err => console.log(err));
    },
    editAddress() {
      this.$axios
        .post('/api/?s=Address.ChangeAddr',
          this.addressInfo
        )
        .then(res => {
          this.$router.push("/myAddress");
        });
    }
  },
  components: {
    Header,
    FormBlock,
    AddressSearch
  }
};
</script>

<style scoped>
.addAddress {
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  padding-top: 45px;
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
